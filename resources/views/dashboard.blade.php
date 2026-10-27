@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h3 class="mb-1">Dashboard</h3>
    @if (! auth()->user()->isAdmin())
        <div class="text-muted mb-3">Data transaksi yang tampil adalah milik Anda.</div>
    @else
        <div class="mb-3"></div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Total Produk</div>
                    <div class="fs-3 fw-bold">{{ $totalProducts }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Stok Rendah</div>
                    <div class="fs-3 fw-bold text-danger">{{ $lowStockProducts->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Transaksi Penjualan</div>
                    <div class="fs-3 fw-bold">{{ $salesCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Transaksi Pembelian</div>
                    <div class="fs-3 fw-bold">{{ $purchasesCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Total Penjualan</div>
                    <div class="fs-3 fw-bold text-success">Rp {{ number_format($salesTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Total Pembelian</div>
                    <div class="fs-3 fw-bold">Rp {{ number_format($purchasesTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Produk Stok Rendah</div>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Min.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td class="text-end text-danger fw-bold">{{ $product->stock }}</td>
                                <td class="text-end">{{ $product->min_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Semua stok aman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">Penjualan Terbaru</div>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_no }}</a></td>
                                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="text-end">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if ($sale->status === 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-secondary">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection