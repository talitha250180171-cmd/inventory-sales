@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <h3 class="mb-3">Tambah Kategori</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                @include('categories._form')
            </form>
        </div>
    </div>
@endsection