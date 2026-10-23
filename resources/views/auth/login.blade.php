<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - MDT Al-Harus</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset('images/logo-title.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Login CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('images/logo-alharus.png') }}" alt="MDT Al-Harus" class="logo">
            <h1>MDT Al-Harus</h1>
            <p>Login Admin Panel</p>
            <span class="admin-badge">🔐 Admin Only</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email" class="@error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@mdtalharus.com" required autofocus>
                </div>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" id="password" class="@error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-options">
                <label>
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>
        </form>
    </div>
</body>
</html>