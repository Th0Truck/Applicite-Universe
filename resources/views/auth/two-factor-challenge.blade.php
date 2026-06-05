<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Universe</title>
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
            text-align: center;
            font-size: 20px;
            letter-spacing: 2px;
            font-weight: 600;
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
        .info-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 20px;
        }
    </style>
</head>
	<body class="has-universe-topbar">
	    @include('components.topbar')

	    <div class="auth-container">
        <div class="auth-header">
            <h1>Two-Factor Authentication</h1>
            <p class="text-muted">Enter your 6-digit code</p>
        </div>

        <p class="info-text">
            Enter the 6-digit code from your authenticator app.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Invalid code:</strong> Please try again
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div class="mb-4">
                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                       id="code" name="code" placeholder="000000" required autofocus 
                       maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
                @error('code')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Verify Code</button>
        </form>

        <div class="auth-footer">
            Lost your authenticator? <a href="{{ route('two-factor.recovery-code') }}">Use a recovery code</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Only allow numbers
        document.getElementById('code').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
