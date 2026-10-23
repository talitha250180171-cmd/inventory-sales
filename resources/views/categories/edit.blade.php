@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <h3 class="mb-3">Edit Kategori</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.update', $category) }}">
                @csrf
                @method('PUT')
                @include('categories._form')
            </form>
        </div>
    </div>
@endsection