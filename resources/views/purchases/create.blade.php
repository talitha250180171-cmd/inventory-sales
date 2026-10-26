@extends('layouts.app')

@section('title', 'Pembelian Baru')

@section('content')
    <h3 class="mb-3">Pembelian Baru</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $oldItems = old('items', [['product_id' => '', 'quantity' => 1, 'unit_price' => '']]);
        $nextIndex = max(array_keys($oldItems)) + 1;
    @endphp

    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="form-select" required>
                            <option value="">-- Pilih supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="purchase_date" class="form-label">Tanggal</label>
                        <input type="date" id="purchase_date" name="purchase_date"
                               value="{{ old('purchase_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Item Pembelian</span>
                <button type="button" id="add-row" class="btn btn-sm btn-outline-primary">+ Tambah Baris</button>
            </div>
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Beli (Rp)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    @foreach ($oldItems as $i => $item)
                        <tr class="item-row">
                            <td>
                                <select name="items[{{ $i }}][product_id]" class="form-select" required>
                                    <option value="">-- Pilih produk --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            @selected(($item['product_id'] ?? '') == $product->id)>
                                            {{ $product->sku }} - {{ $product->name }} (stok: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 140px;">
                                <input type="number" min="1" name="items[{{ $i }}][quantity]"
                                       value="{{ $item['quantity'] ?? 1 }}" data-default="1"
                                       class="form-control qty" required>
                            </td>
                            <td style="width: 200px;">
                                <input type="number" min="0" step="0.01" name="items[{{ $i }}][unit_price]"
                                       value="{{ $item['unit_price'] ?? '' }}" data-default=""
                                       class="form-control price" required>
                            </td>
                            <td style="width: 60px;">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Perkiraan total</th>
                        <th colspan="2" id="total-preview">Rp 0</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-text mb-3">Total akhir dihitung ulang oleh server saat disimpan.</div>

        <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
    </form>

    <script>
        (function () {
            var body = document.getElementById('items-body');
            var index = {{ $nextIndex }};

            function updateTotal() {
                var total = 0;
                body.querySelectorAll('.item-row').forEach(function (row) {
                    var qty = parseFloat(row.querySelector('.qty').value) || 0;
                    var price = parseFloat(row.querySelector('.price').value) || 0;
                    total += qty * price;
                });
                document.getElementById('total-preview').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            document.getElementById('add-row').addEventListener('click', function () {
                var clone = body.querySelector('.item-row').cloneNode(true);
                clone.querySelectorAll('select, input').forEach(function (el) {
                    el.name = el.name.replace(/items\[\d+\]/, 'items[' + index + ']');
                    if (el.tagName === 'SELECT') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = el.dataset.default;
                    }
                });
                body.appendChild(clone);
                index++;
                updateTotal();
            });

            body.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-row') && body.querySelectorAll('.item-row').length > 1) {
                    e.target.closest('.item-row').remove();
                    updateTotal();
                }
            });

            body.addEventListener('input', updateTotal);
            body.addEventListener('change', updateTotal);
            updateTotal();
        })();
    </script>
@endsection