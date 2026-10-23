<div class="row">
    <div class="col-md-4 mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" id="sku" name="sku"
               value="{{ old('sku', $product->sku ?? '') }}"
               class="form-control @error('sku') is-invalid @enderror" required>
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-8 mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $product->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Kategori</label>
    <select id="category_id" name="category_id"
            class="form-select @error('category_id') is-invalid @enderror" required>
        <option value="">-- Pilih kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="price" class="form-label">Harga (Rp)</label>
        <input type="number" step="0.01" min="0" id="price" name="price"
               value="{{ old('price', $product->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror" required>
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        @if (isset($product))
            <label class="form-label">Stok saat ini</label>
            <input type="text" class="form-control" value="{{ $product->stock }}" disabled>
            <div class="form-text">Stok berubah lewat pembelian dan penjualan.</div>
        @else
            <label for="stock" class="form-label">Stok Awal</label>
            <input type="number" min="0" id="stock" name="stock"
                   value="{{ old('stock', 0) }}"
                   class="form-control @error('stock') is-invalid @enderror" required>
            @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    <div class="col-md-4 mb-3">
        <label for="min_stock" class="form-label">Stok Minimum</label>
        <input type="number" min="0" id="min_stock" name="min_stock"
               value="{{ old('min_stock', $product->min_stock ?? 5) }}"
               class="form-control @error('min_stock') is-invalid @enderror" required>
        @error('min_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Deskripsi</label>
    <textarea id="description" name="description" rows="3"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>