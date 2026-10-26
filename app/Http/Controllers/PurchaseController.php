<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $purchases = Purchase::with(['supplier', 'user'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->search, fn ($q, $s) => $q->where('invoice_no', 'like', "%{$s}%"))
            ->when($request->supplier_id, fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('purchase_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('purchase_date', '<=', $d))
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create(): View
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'distinct',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
        ], [
            'items.required' => 'Tambahkan minimal satu produk.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh dipilih dua kali.',
        ]);

        try {
            $purchase = DB::transaction(function () use ($data, $request) {
                // Kunci baris produk selama transaksi supaya stok tidak bentrok
                $products = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $purchase = Purchase::create([
                    'invoice_no' => 'TMP-' . Str::uuid(),
                    'supplier_id' => $data['supplier_id'],
                    'user_id' => $request->user()->id,
                    'purchase_date' => $data['purchase_date'],
                    'total_amount' => 0,
                    'status' => 'completed',
                    'notes' => $data['notes'] ?? null,
                ]);

                $total = 0;

                foreach ($data['items'] as $item) {
                    $product = $products->get($item['product_id']);

                    if (! $product) {
                        throw new RuntimeException('Ada produk yang sudah tidak tersedia. Silakan ulangi.');
                    }

                    // Subtotal dihitung di server
                    $subtotal = round($item['quantity'] * $item['unit_price'], 2);

                    $purchase->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                    ]);

                    $product->increment('stock', $item['quantity']);

                    $total += $subtotal;
                }

                $purchase->update([
                    'invoice_no' => sprintf('PO-%s-%05d', now()->format('Ymd'), $purchase->id),
                    'total_amount' => $total,
                ]);

                return $purchase;
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Pembelian berhasil dicatat dan stok sudah bertambah.');
    }

    public function show(Request $request, Purchase $purchase): View
    {
        $user = $request->user();

        abort_unless($user->isAdmin() || $purchase->user_id === $user->id, 403);

        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        try {
            DB::transaction(function () use ($purchase) {
                $purchase = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

                if ($purchase->status === 'cancelled') {
                    throw new RuntimeException('Pembelian ini sudah dibatalkan sebelumnya.');
                }

                $items = $purchase->items()->get();

                $products = Product::withTrashed()
                    ->whereIn('id', $items->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Cek dulu semua item sebelum ada stok yang diubah
                foreach ($items as $item) {
                    $product = $products->get($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw new RuntimeException(
                            "Tidak bisa dibatalkan: stok {$product->name} hanya {$product->stock}, "
                            . "sedangkan pembelian ini menambah {$item->quantity}. Barangnya sudah terjual."
                        );
                    }
                }

                foreach ($items as $item) {
                    $products->get($item->product_id)->decrement('stock', $item->quantity);
                }

                $purchase->update(['status' => 'cancelled']);
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Pembelian dibatalkan dan stok dikembalikan.');
    }
}