@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $purchase->invoice_no }}</h3>
        <div>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
            @if (auth()->user()->isAdmin() && $purchase->status === 'completed')
                <form method="POST" action="{{ route('purchases.cancel', $purchase) }}" class="d-inline"
                      onsubmit="return confirm('Batalkan pembelian ini? Stok akan dikurangi kembali.')">
                    @csrf
                    <button type="submit" class="btn btn-danger">Batalkan Pembelian</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <div class="text-muted">Tanggal</div>
                    <div>{{ $purchase->purchase_date->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="text-muted">Supplier</div>
                    <div>{{ $purchase->supplier->name }}</div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="text-muted">Petugas</div>
                    <div>{{ $purchase->user->name }}</div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="text-muted">Status</div>
                    @if ($purchase->status === 'completed')
                        <span class="badge bg-success">Selesai</span>
                    @else
                        <span class="badge bg-secondary">Dibatalkan</span>
                    @endif
                </div>
            </div>
            @if ($purchase->notes)
                <div class="mt-2">
                    <div class="text-muted">Catatan</div>
                    <div>{{ $purchase->notes }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Produk</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end">Harga Beli</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->sku }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th class="text-end">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection