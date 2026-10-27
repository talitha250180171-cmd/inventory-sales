@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .hero { background: #2563eb; color: #fff; border-radius: 14px; }
        .hero .btn { padding: .6rem 1.1rem; font-weight: 600; }
        .hero .btn-light { color: #2563eb; }

        .stat-card .icon {
            width: 52px; height: 52px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem; flex-shrink: 0;
        }
        .stat-label { font-size: .85rem; color: #334155; }
        .stat-value { font-weight: 700; line-height: 1.25; color: var(--ink); }
        .stat-note { font-size: .78rem; color: var(--muted); }

        .tone-blue   { background: #dbeafe; color: #2563eb; }
        .tone-green  { background: #dcfce7; color: #16a34a; }
        .tone-violet { background: #ede9fe; color: #7c3aed; }
        .tone-amber  { background: #fef3c7; color: #d97706; }
        .tone-indigo { background: #e0e7ff; color: #4f46e5; }
        .tone-rose   { background: #ffe4e6; color: #e11d48; }
        .tone-sky    { background: #e0f2fe; color: #0284c7; }
        .tone-orange { background: #ffedd5; color: #ea580c; }

        .section-title { font-weight: 700; font-size: 1.05rem; }
        .pill {
            display: inline-block; padding: .2rem .7rem;
            border-radius: 999px; font-size: .75rem; font-weight: 600;
        }
        .pill-success { background: #dcfce7; color: #15803d; }
        .pill-muted   { background: #e2e8f0; color: #475569; }
        .pill-danger  { background: #ef4466; color: #fff; min-width: 2rem; text-align: center; }
    </style>

    @php
        $money = [
            ['label' => 'Penjualan Hari Ini',  'value' => $salesToday,     'icon' => 'bi-cart3',           'tone' => 'blue',   'note' => 'Transaksi selesai hari ini'],
            ['label' => 'Penjualan Bulan Ini', 'value' => $salesMonth,     'icon' => 'bi-calendar2-check', 'tone' => 'green',  'note' => 'Transaksi selesai bulan ini'],
            ['label' => 'Total Penjualan',     'value' => $salesTotal,     'icon' => 'bi-graph-up-arrow',  'tone' => 'violet', 'note' => 'Semua transaksi selesai'],
            ['label' => 'Total Pembelian',     'value' => $purchasesTotal, 'icon' => 'bi-wallet2',         'tone' => 'amber',  'note' => 'Semua pembelian selesai'],
        ];

        $lowCount = $lowStockProducts->count();

        $counts = [
            ['label' => 'Total Produk',        'value' => $totalProducts,  'icon' => 'bi-box-seam',             'tone' => 'indigo', 'note' => 'Produk terdaftar'],
            ['label' => 'Produk Stok Rendah',  'value' => $lowCount,       'icon' => 'bi-exclamation-triangle', 'tone' => 'rose',   'note' => $lowCount > 0 ? 'Perlu perhatian' : 'Semua stok aman'],
            ['label' => 'Transaksi Penjualan', 'value' => $salesCount,     'icon' => 'bi-receipt',              'tone' => 'sky',    'note' => 'Transaksi selesai'],
            ['label' => 'Transaksi Pembelian', 'value' => $purchasesCount, 'icon' => 'bi-truck',                'tone' => 'orange', 'note' => 'Transaksi selesai'],
        ];
    @endphp

    {{-- Banner --}}
    <div class="hero p-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="small opacity-75">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</div>
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
            <a href="{{ route('sales.create') }}" class="btn btn-light">
                <i class="bi bi-plus-lg"></i> Penjualan Baru
            </a>
            <a href="{{ route('purchases.create') }}" class="btn btn-outline-light">
                <i class="bi bi-file-earmark-text"></i> Pembelian Baru
            </a>
        </div>
    </div>

    {{-- Baris 1: nominal --}}
    <div class="row g-3 mb-3">
        @foreach ($money as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="icon tone-{{ $card['tone'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                        <div>
                            <div class="stat-label">{{ $card['label'] }}</div>
                            <div class="stat-value fs-4">Rp {{ number_format($card['value'], 0, ',', '.') }}</div>
                            <div class="stat-note">{{ $card['note'] }}</div>
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
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="icon tone-{{ $card['tone'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                        <div>
                            <div class="stat-label">{{ $card['label'] }}</div>
                            <div class="stat-value fs-3">{{ $card['value'] }}</div>
                            <div class="stat-note">{{ $card['note'] }}</div>
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
                <div class="d-flex justify-content-between align-items-center px-4 py-3">
                    <span class="section-title">
                        <i class="bi bi-exclamation-triangle text-danger me-2"></i>Produk Stok Rendah
                    </span>
                    <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="small fw-medium text-decoration-none">
                        Lihat Semua <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Stok</th>
                                <th>Min.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td><span class="pill pill-danger">{{ $product->stock }}</span></td>
                                    <td>{{ $product->min_stock }}</td>
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
        </div>

        {{-- Penjualan terbaru --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center px-4 py-3">
                    <span class="section-title">
                        <i class="bi bi-receipt text-primary me-2"></i>Penjualan Terbaru
                    </span>
                    <a href="{{ route('sales.index') }}" class="small fw-medium text-decoration-none">
                        Lihat Semua <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>No. Faktur</th>
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
                                            <span class="pill pill-success">Selesai</span>
                                        @else
                                            <span class="pill pill-muted">Dibatalkan</span>
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
    </div>
@endsection