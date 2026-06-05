<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Universe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 5px;
            padding: 12px;
            font-size: 14px;
        }
        .btn-primary {
            border-radius: 5px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
        }
        .auth-footer a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
	<body class="has-universe-topbar">
	    @include('components.topbar')

	    <div class="auth-container">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p class="text-muted">Sign in to your account</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Login failed:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
