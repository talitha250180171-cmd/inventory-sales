@extends('layouts.app')

@section('title', 'Penjualan Baru')

@section('content')
    <h3 class="mb-3">Penjualan Baru</h3>

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
        $oldItems = old('items', [['product_id' => '', 'quantity' => 1]]);
        $nextIndex = max(array_keys($oldItems)) + 1;
    @endphp

    <form method="POST" action="{{ route('sales.store') }}">
        @csrf

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="customer_name" class="form-label">Nama Pelanggan (opsional)</label>
                        <input type="text" id="customer_name" name="customer_name"
                               value="{{ old('customer_name') }}" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sale_date" class="form-label">Tanggal</label>
                        <input type="date" id="sale_date" name="sale_date"
                               value="{{ old('sale_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="notes" class="form-label">Catatan</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Item Penjualan</span>
                <button type="button" id="add-row" class="btn btn-sm btn-outline-primary">+ Tambah Baris</button>
            </div>
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga satuan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    @foreach ($oldItems as $i => $item)
                        <tr class="item-row">
                            <td>
                                <select name="items[{{ $i }}][product_id]" class="form-select product-select" required>
                                    <option value="" data-price="0">-- Pilih produk --</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}"
                                            @selected(($item['product_id'] ?? '') == $product->id)>
                                            {{ $product->sku }} - {{ $product->name }} (stok: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 140px;">
                                <input type="number" min="1" name="items[{{ $i }}][quantity]"
                                       value="{{ $item['quantity'] ?? 1 }}" class="form-control qty" required>
                            </td>
                            <td style="width: 200px;" class="unit-price-text">-</td>
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

        <div class="form-text mb-3">Harga diambil dari data produk, dan total akhir dihitung ulang oleh server.</div>

        <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
    </form>

    <script>
        (function () {
            var body = document.getElementById('items-body');
            var index = {{ $nextIndex }};

            function rupiah(n) {
                return 'Rp ' + n.toLocaleString('id-ID');
            }

            function updateTotal() {
                var total = 0;
                body.querySelectorAll('.item-row').forEach(function (row) {
                    var select = row.querySelector('.product-select');
                    var price = parseFloat(select.options[select.selectedIndex].dataset.price) || 0;
                    var qty = parseFloat(row.querySelector('.qty').value) || 0;
                    row.querySelector('.unit-price-text').textContent = price > 0 ? rupiah(price) : '-';
                    total += price * qty;
                });
                document.getElementById('total-preview').textContent = rupiah(total);
            }

            document.getElementById('add-row').addEventListener('click', function () {
                var clone = body.querySelector('.item-row').cloneNode(true);
                clone.querySelectorAll('select, input').forEach(function (el) {
                    el.name = el.name.replace(/items\[\d+\]/, 'items[' + index + ']');
                    if (el.tagName === 'SELECT') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = 1;
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