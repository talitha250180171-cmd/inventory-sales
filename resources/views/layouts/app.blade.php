<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Serba Ada Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --brand-soft: #eff6ff;
            --sidebar-w: 250px;
            --bs-primary: #2563eb;
            --bs-primary-rgb: 37, 99, 235;
            --bs-link-color: #2563eb;
            --bs-link-color-rgb: 37, 99, 235;
            --bs-link-hover-color: #1d4ed8;
            --bs-link-hover-color-rgb: 29, 78, 216;
        }

        body { background: #f5f8ff; }

        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: #fff;
            border-right: 1px solid #e5eaf5;
            display: flex; flex-direction: column;
            padding: 1.25rem 1rem;
            overflow-y: auto;
            z-index: 1030;
        }
        .sidebar .brand {
            display: flex; align-items: center; gap: .5rem;
            font-weight: 700; font-size: 1.15rem;
            color: var(--brand); text-decoration: none;
            margin-bottom: 1.25rem;
        }
        .sidebar .brand i { font-size: 1.5rem; }
        .sidebar .menu-label {
            font-size: .7rem; text-transform: uppercase;
            letter-spacing: .08em; color: #94a3b8;
            margin: 1.25rem .75rem .4rem;
        }
        .sidebar .nav-link {
            display: flex; align-items: center; gap: .65rem;
            color: #475569; font-weight: 500;
            padding: .55rem .75rem; border-radius: .6rem;
        }
        .sidebar .nav-link:hover { background: var(--brand-soft); color: var(--brand); }
        .sidebar .nav-link.active { background: var(--brand); color: #fff; }

        .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--brand-soft); color: var(--brand);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
        }

        .main { margin-left: var(--sidebar-w); padding: 1.75rem 2rem; min-height: 100vh; }

        .card { border: 0; border-radius: .9rem; overflow: hidden; }
        .table thead th {
            background: #f8faff; color: #64748b;
            font-weight: 600; font-size: .85rem;
            border-bottom: 1px solid #e5eaf5;
        }
        .btn-primary {
            --bs-btn-bg: var(--brand); --bs-btn-border-color: var(--brand);
            --bs-btn-hover-bg: var(--brand-dark); --bs-btn-hover-border-color: var(--brand-dark);
            --bs-btn-active-bg: var(--brand-dark); --bs-btn-active-border-color: var(--brand-dark);
        }
        .btn-outline-primary {
            --bs-btn-color: var(--brand); --bs-btn-border-color: var(--brand);
            --bs-btn-hover-bg: var(--brand); --bs-btn-hover-border-color: var(--brand);
            --bs-btn-active-bg: var(--brand); --bs-btn-active-border-color: var(--brand);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
        }

        @media (max-width: 991.98px) {
            .sidebar { position: static; width: 100%; border-right: 0; border-bottom: 1px solid #e5eaf5; }
            .main { margin-left: 0; padding: 1.25rem 1rem; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <a class="brand" href="{{ route('dashboard') }}">
        <i class="bi bi-shop-window"></i> Serba Ada Mart
    </a>

    @auth
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="menu-label">Transaksi</div>
            <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                <i class="bi bi-cart-check"></i> Penjualan
            </a>
            <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                <i class="bi bi-bag-plus"></i> Pembelian
            </a>

            <div class="menu-label">Data</div>
            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>
            @if (auth()->user()->isAdmin())
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="bi bi-tags"></i> Kategori
                </a>
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            @endif
        </nav>

        <div class="mt-auto pt-3 border-top">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="lh-sm">
                    <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ auth()->user()->role }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    @endauth
</aside>

<main class="main">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>

</body>
</html>