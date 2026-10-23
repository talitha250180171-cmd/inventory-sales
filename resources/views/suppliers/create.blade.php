@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
    <h3 class="mb-3">Tambah Supplier</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf
                @include('suppliers._form')
            </form>
        </div>
    </div>
@endsection