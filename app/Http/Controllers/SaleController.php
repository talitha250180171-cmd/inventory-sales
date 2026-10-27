<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $sales = Sale::with('user')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('sale_date', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('sale_date', '<=', $d))
            ->latest('sale_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        return view('sales.create', [
            'products' => Product::where('stock', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'sale_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'distinct',
                Rule::exists('products', 'id')->whereNull('deleted_at'),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100000'],
        ], [
            'items.required' => 'Tambahkan minimal satu produk.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh dipilih dua kali.',
        ]);

        try {
            $sale = DB::transaction(function () use ($data, $request) {
                // Kunci baris produk supaya stok tidak bentrok antar transaksi
                $products = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $sale = Sale::create([
                    'invoice_no' => 'TMP-' . Str::uuid(),
                    'user_id' => $request->user()->id,
                    'customer_name' => $data['customer_name'] ?? null,
                    'sale_date' => $data['sale_date'],
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

                    // Stok tidak cukup: lempar error, seluruh transaksi di-rollback
                    if ($product->stock < $item['quantity']) {
                        throw new RuntimeException(
                            "Stok {$product->name} tidak cukup. Tersedia {$product->stock}, diminta {$item['quantity']}."
                        );
                    }

                    // Harga diambil dari database, bukan dari form
                    $unitPrice = $product->price;
                    $subtotal = round($item['quantity'] * $unitPrice, 2);

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $item['quantity']);

                    $total += $subtotal;
                }

                $sale->update([
                    'invoice_no' => sprintf('INV-%s-%05d', now()->format('Ymd'), $sale->id),
                    'total_amount' => $total,
                ]);

                return $sale;
            });
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Penjualan berhasil dicatat dan stok sudah berkurang.');
    }

    public function show(Request $request, Sale $sale): View
    {
        $user = $request->user();

        abort_unless($user->isAdmin() || $sale->user_id === $user->id, 403);

        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function () use ($sale) {
                $sale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

                if ($sale->status === 'cancelled') {
                    throw new RuntimeException('Penjualan ini sudah dibatalkan sebelumnya.');
                }

                $items = $sale->items()->get();

                $products = Product::withTrashed()
                    ->whereIn('id', $items->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $products->get($item->product_id)->increment('stock', $item->quantity);
                }

                $sale->update(['status' => 'cancelled']);
            });
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Penjualan dibatalkan dan stok dikembalikan.');
    }
}