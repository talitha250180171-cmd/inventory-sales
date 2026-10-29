@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Produk</h3>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>
        @endif
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="row g-2 mb-3 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="form-control" placeholder="Cari nama atau SKU...">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="low_stock" name="low_stock" value="1"
                       @checked(request()->boolean('low_stock'))>
                <label class="form-check-label" for="low_stock">Stok rendah saja</label>
            </div>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-secondary">Filter</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-dark">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;">Foto</th>
                        <th>SKU</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Stok</th>
                        @if (auth()->user()->isAdmin())
                            <th style="width: 160px;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                @if ($product->image)
                                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}"
                                         style="width: 44px; height: 44px; object-fit: cover; border-radius: .5rem; border: 1px solid #e5eaf3;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center text-secondary"
                                         style="width: 44px; height: 44px; border-radius: .5rem; background: #f1f5fb;">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td class="text-end">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="text-end">
                                {{ $product->stock }}
                                @if ($product->isLowStock())
                                    <span class="badge bg-danger">Rendah</span>
                                @endif
                            </td>
                            @if (auth()->user()->isAdmin())
                                <td>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
@endsection