<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Serba Ada Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 50%, #dbeafe 100%);
        }
        .login-card { width: 100%; max-width: 420px; border: 0; border-radius: 1rem; }
        .logo {
            width: 64px; height: 64px; border-radius: 50%;
            background: #eff6ff; color: #2563eb;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; margin: 0 auto 1rem;
        }
        .btn-primary { --bs-btn-bg: #2563eb; --bs-btn-border-color: #2563eb;
            --bs-btn-hover-bg: #1d4ed8; --bs-btn-hover-border-color: #1d4ed8; }
        .form-control:focus { border-color: #2563eb; box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .15); }
    </style>
</head>
<body>
<div class="card login-card shadow">
    <div class="card-body p-4 p-md-5">
        <div class="logo"><i class="bi bi-shop-window"></i></div>
        <h4 class="text-center fw-bold mb-1">Serba Ada Mart</h4>
        <p class="text-center text-muted mb-4">Sistem Inventori &amp; Penjualan</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-4">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
        </form>
    </div>
</div>
</body>
</html>