@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <h3 class="mb-3">Tambah Produk</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                @include('products._form')
            </form>
        </div>
    </div>
@endsection