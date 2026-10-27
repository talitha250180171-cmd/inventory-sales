<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - Serba Ada Mart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #2563eb;
            --brand-dark: #1d4ed8;
            --brand-soft: #eff6ff;
            --ink: #172554;
            --muted: #64748b;
            --line: #e5eaf3;
            --page: #f4f7fc;
            --sidebar-w: 260px;

            --bs-body-font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --bs-body-color: #172554;
            --bs-body-bg: #f4f7fc;
            --bs-secondary-color: #64748b;
            --bs-border-color: #e5eaf3;
            --bs-primary: #2563eb;
            --bs-primary-rgb: 37, 99, 235;
            --bs-link-color: #2563eb;
            --bs-link-color-rgb: 37, 99, 235;
            --bs-link-hover-color: #1d4ed8;
            --bs-link-hover-color-rgb: 29, 78, 216;
            --bs-box-shadow-sm: 0 1px 2px rgba(23, 37, 84, .05);
        }

        body { background: var(--page); color: var(--ink); }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: #fff;
            border-right: 1px solid var(--line);
            display: flex; flex-direction: column;
            padding: 1.25rem 1rem;
            overflow-y: auto;
            z-index: 1030;
        }
        .sidebar .brand {
            display: flex; align-items: center; gap: .6rem;
            font-weight: 700; font-size: 1.2rem;
            color: var(--brand); text-decoration: none;
            margin: .25rem .5rem 1.5rem;
        }
        .sidebar .brand i { font-size: 1.6rem; }
        .sidebar .menu-label {
            font-size: .72rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: .08em; color: var(--muted);
            margin: 1.4rem .85rem .5rem;
        }
        .sidebar .nav-link {
            display: flex; align-items: center; gap: .75rem;
            color: #334155; font-weight: 500; font-size: .95rem;
            padding: .65rem .85rem; margin-bottom: .15rem;
            border-radius: .65rem;
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 1.25rem; text-align: center; }
        .sidebar .nav-link:hover { background: var(--brand-soft); color: var(--brand); }
        .sidebar .nav-link.active { background: var(--brand); color: #fff; }

        .avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: #dbeafe; color: var(--brand);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; flex-shrink: 0;
        }
        .btn-logout {
            background: #fff; border: 1px solid var(--line); color: #334155;
            border-radius: .6rem; font-weight: 500;
        }
        .btn-logout:hover { background: var(--brand-soft); color: var(--brand); border-color: #bfdbfe; }

        /* Konten utama */
        .main {
            margin-left: var(--sidebar-w);
            padding: 1.75rem 2rem;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }
        .main-content { flex: 1; }

        /* Kartu dan tabel (berlaku di semua halaman) */
        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--line);
            font-weight: 600;
        }
        .table { --bs-table-bg: transparent; --bs-table-border-color: var(--line); color: var(--ink); }
        .table > :not(caption) > * > * { padding: .8rem 1rem; }
        .table thead th {
            background: #f1f5fb; color: var(--muted);
            font-weight: 600; font-size: .8rem;
            border-bottom: 1px solid var(--line);
        }
        .card .table th:first-child, .card .table td:first-child { padding-left: 1.5rem; }
        .card .table th:last-child, .card .table td:last-child { padding-right: 1.5rem; }

        /* Tombol dan form */
        .btn { border-radius: .6rem; font-weight: 500; }
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
        .form-control, .form-select { border-color: #d6deee; border-radius: .6rem; }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15);
        }

        @media (max-width: 991.98px) {
            .sidebar { position: static; width: 100%; border-right: 0; border-bottom: 1px solid var(--line); }
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
                <i class="bi bi-cart3"></i> Penjualan
            </a>
            <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                <i class="bi bi-bag-check"></i> Pembelian
            </a>

            <div class="menu-label">Data</div>
            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                <i class="bi bi-box"></i> Produk
            </a>
            @if (auth()->user()->isAdmin())
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="bi bi-grid"></i> Kategori
                </a>
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-people"></i> Supplier
                </a>
            @endif
        </nav>

        <div class="mt-auto pt-3 border-top">
            <div class="d-flex align-items-center gap-2 mb-3 px-1">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="lh-sm">
                    <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                    <div class="small text-muted">{{ auth()->user()->role }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-logout btn-sm w-100 py-2">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    @endauth
</aside>

<main class="main">
    <div class="main-content">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <footer class="d-flex justify-content-between text-muted small pt-4">
        <span>Serba Ada Mart &copy; {{ date('Y') }}</span>
        <span>Versi 1.0</span>
    </footer>
</main>

</body>
</html>