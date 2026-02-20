<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sachipa Curtain - Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4b0082, #1e3c72);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .shop-title {
            font-weight: bold;
            color: #4b0082;
            letter-spacing: 1px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
        }

        .btn-custom {
            background: #4b0082;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background: #2e0854;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .remember-text {
            font-size: 14px;
        }

        .register-link {
            color: #4b0082;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Decorative top curtain effect */
        .curtain-top {
            position: absolute;
            top: 0;
            width: 100%;
            height: 120px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4), transparent);
        }
    </style>
</head>
<body>

<div class="curtain-top"></div>

<div class="login-card">

    <div class="text-center mb-4">
        <h2 class="shop-title">Sachipa Curtain</h2>
        <p class="text-muted">Elegant Curtains for Elegant Homes</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input 
                type="email" 
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Enter your email"
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input 
                type="password" 
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Enter your password"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="remember" id="remember">
            <label class="form-check-label remember-text" for="remember">
                Remember Me
            </label>
        </div>

        <!-- Login Button -->
        <div class="d-grid">
            <button type="submit" class="btn btn-custom text-white">
                Login
            </button>
        </div>

    </form>

    <!-- Register -->
    <div class="text-center mt-4">
        <small>
            Don't have an account?
            <a href="{{ route('register') }}" class="register-link">Register</a>
        </small>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
