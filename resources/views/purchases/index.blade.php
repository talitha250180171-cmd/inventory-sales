@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Pembelian</h3>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Pembelian Baru</a>
    </div>

    <form method="GET" action="{{ route('purchases.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari no. faktur...">
        </div>
        <div class="col-md-3">
            <select name="supplier_id" class="form-select">
                <option value="">Semua supplier</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua status</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" title="Dari tanggal">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" title="Sampai tanggal">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-outline-secondary">Filter</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-dark">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    @if (auth()->user()->isAdmin())
                        <th>Petugas</th>
                    @endif
                    <th class="text-end">Total</th>
                    <th>Status</th>
                    <th style="width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->invoice_no }}</td>
                        <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                        <td>{{ $purchase->supplier->name }}</td>
                        @if (auth()->user()->isAdmin())
                            <td>{{ $purchase->user->name }}</td>
                        @endif
                        <td class="text-end">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @if ($purchase->status === 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-secondary">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada pembelian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $purchases->links() }}
    </div>
@endsection