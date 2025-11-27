<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Grupo Terra</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Ícones Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #730f16, #9b252d);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .text{
            color: #730f13;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            padding: 2rem;
            width: 100%;
            max-width: 420px;
        }
        .brand-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background-color: #9b252d;
            border: none;
        }
        .btn-primary:hover {
            background-color: #730f16;
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <h4 class="mb-4 text">Grupo <strong>Terra</strong></h4>

        {{-- FORM LOGIN --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-floating mb-3">
                <input type="email" name="email" id="email" class="form-control" placeholder=" " value="{{ old('email') }}" required autofocus>
                <label for="email">Endereço de Email</label>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-floating mb-3">
                <input type="password" name="password" id="password" class="form-control" placeholder=" " required>
                <label for="password">Senha</label>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                    <label class="form-check-label" for="remember_me">Lembrar-me</label>
                </div>
                @if (Route::has('password.request'))
                    <a class="text-decoration-none small" href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>

            @if (Route::has('register'))
                <p class="mt-4 mb-0">
                    <small>Ainda não tem conta? <a href="{{ route('register') }}" class="text fw-semibold">Registre-se</a></small>
                </p>
            @endif
        </form>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
