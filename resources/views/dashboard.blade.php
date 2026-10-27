@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .stat-card .icon {
            width: 48px; height: 48px; border-radius: .8rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
        }
        .tone-blue   { background: #dbeafe; color: #2563eb; }
        .tone-sky    { background: #e0f2fe; color: #0284c7; }
        .tone-indigo { background: #e0e7ff; color: #4f46e5; }
        .tone-amber  { background: #fef3c7; color: #d97706; }
        .tone-rose   { background: #ffe4e6; color: #e11d48; }
        .tone-violet { background: #ede9fe; color: #7c3aed; }
        .hero {
            background: linear-gradient(120deg, #2563eb, #3b82f6 60%, #60a5fa);
            color: #fff; border-radius: 1rem;
        }
    </style>

    @php
        // Kartu uang (baris pertama)
        $money = [
            ['label' => 'Penjualan Hari Ini',  'value' => $salesToday,     'icon' => 'bi-cash-coin',        'tone' => 'blue'],
            ['label' => 'Penjualan Bulan Ini', 'value' => $salesMonth,     'icon' => 'bi-calendar2-check',  'tone' => 'sky'],
            ['label' => 'Total Penjualan',     'value' => $salesTotal,     'icon' => 'bi-graph-up-arrow',   'tone' => 'indigo'],
            ['label' => 'Total Pembelian',     'value' => $purchasesTotal, 'icon' => 'bi-bag-plus',         'tone' => 'amber'],
        ];

        // Kartu jumlah (baris kedua)
        $counts = [
            ['label' => 'Total Produk',          'value' => $totalProducts,                 'icon' => 'bi-box-seam',      'tone' => 'violet'],
            ['label' => 'Produk Stok Rendah',    'value' => $lowStockProducts->count(),     'icon' => 'bi-exclamation-triangle', 'tone' => 'rose'],
            ['label' => 'Transaksi Penjualan',   'value' => $salesCount,                    'icon' => 'bi-receipt',       'tone' => 'blue'],
            ['label' => 'Transaksi Pembelian',   'value' => $purchasesCount,                'icon' => 'bi-truck',         'tone' => 'amber'],
        ];
    @endphp

    {{-- Banner sambutan --}}
    <div class="hero p-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="opacity-75 small">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
            <h3 class="fw-bold mb-1">Halo, {{ auth()->user()->name }}!</h3>
            <div class="opacity-90">
                @if (auth()->user()->isAdmin())
                    Ringkasan seluruh transaksi Serba Ada Mart.
                @else
                    Ringkasan transaksi milik Anda.
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.create') }}" class="btn btn-light fw-semibold">
                <i class="bi bi-cart-plus"></i> Penjualan Baru
            </a>
            <a href="{{ route('purchases.create') }}" class="btn btn-outline-light fw-semibold">
                <i class="bi bi-bag-plus"></i> Pembelian Baru
            </a>
        </div>
    </div>

    {{-- Baris 1: nominal uang --}}
    <div class="row g-3 mb-3">
        @foreach ($money as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon tone-{{ $card['tone'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-5 fw-bold">Rp {{ number_format($card['value'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Baris 2: jumlah --}}
    <div class="row g-3 mb-4">
        @foreach ($counts as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="icon tone-{{ $card['tone'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-3 fw-bold">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        {{-- Stok rendah --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold"><i class="bi bi-exclamation-triangle text-danger"></i> Produk Stok Rendah</span>
                    <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                <table class="table mb-0 align-middle">
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
                                <td class="text-end"><span class="badge bg-danger">{{ $product->stock }}</span></td>
                                <td class="text-end">{{ $product->min_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle"></i> Semua stok aman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Penjualan terbaru --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold"><i class="bi bi-receipt text-primary"></i> Penjualan Terbaru</span>
                    <a href="{{ route('sales.index') }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                <table class="table mb-0 align-middle">
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
                                <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->invoice_no }}</a></td>
                                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td class="text-end">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if ($sale->status === 'completed')
                                        <span class="badge bg-primary-subtle text-primary-emphasis">Selesai</span>
                                    @else
                                        <span class="badge bg-secondary">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada penjualan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection