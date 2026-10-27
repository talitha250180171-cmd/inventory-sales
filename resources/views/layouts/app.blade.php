<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventory & Sales')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Inventory &amp; Sales</a>
@auth
    <div class="d-flex gap-3 me-auto ms-4">
        <a class="nav-link text-light" href="{{ route('products.index') }}">Produk</a>
        <a class="nav-link text-light" href="{{ route('purchases.index') }}">Pembelian</a>
        <a class="nav-link text-light" href="{{ route('sales.index') }}">Penjualan</a>
        @if (auth()->user()->isAdmin())
            <a class="nav-link text-light" href="{{ route('categories.index') }}">Kategori</a>
            <a class="nav-link text-light" href="{{ route('suppliers.index') }}">Supplier</a>
        @endif
    </div>
@endauth        @auth
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    {{ auth()->user()->name }}
                    <span class="badge bg-secondary">{{ auth()->user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        @endauth
    </div>
</nav>

<div class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>