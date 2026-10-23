@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Supplier</h3>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Tambah Supplier</a>
    </div>

    <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari nama atau telepon...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Cari</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-dark">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th style="width: 160px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ $supplier->address ?? '-' }}</td>
                        <td>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                  class="d-inline" onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>
@endsection