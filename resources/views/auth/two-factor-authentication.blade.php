<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - Universe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
            min-height: 100vh;
            padding: 32px 16px;
        }

        .setup-container {
            background: white;
            border: 1px solid #e6e8ef;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
            margin: 0 auto;
            max-width: 760px;
            padding: 32px;
        }

        .qr-code svg {
            height: auto;
            max-width: 192px;
            width: 100%;
        }

        .secret-key,
        .recovery-code {
            background: #f1f3f7;
            border-radius: 5px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
            padding: 8px 10px;
            word-break: break-all;
        }
    </style>
</head>
	<body class="has-universe-topbar">
	    @include('components.topbar')

	    <main class="setup-container">
        <h1 class="h3 mb-2">Two-factor authentication</h1>
        <p class="text-muted mb-4">Protect your account with a one-time code from an authenticator app.</p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                Two-factor authentication was updated.
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($user->two_factor_secret === null)
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <button type="submit" class="btn btn-primary">Enable two-factor authentication</button>
            </form>
        @elseif ($user->two_factor_confirmed_at === null)
            <div class="row g-4 align-items-start">
                <div class="col-md-auto qr-code">
                    {!! $user->twoFactorQrCodeSvg() !!}
                </div>
                <div class="col">
                    <h2 class="h5">Scan this QR code</h2>
                    <p class="text-muted">After scanning, enter the six-digit code from your authenticator app to confirm setup.</p>
                    <div class="secret-key mb-3">{{ $secretKey }}</div>

                    <form method="POST" action="{{ route('two-factor.confirm') }}" class="d-flex gap-2 flex-wrap">
                        @csrf
                        <input
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            class="form-control"
                            name="code"
                            placeholder="Authentication code"
                            required
                            style="max-width: 220px;"
                        >
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-success" role="alert">
                Two-factor authentication is enabled.
            </div>

            @if ($recoveryCodes !== [])
                <h2 class="h5 mt-4">Recovery codes</h2>
                <div class="row g-2 mb-4">
                    @foreach ($recoveryCodes as $code)
                        <div class="col-sm-6">
                            <div class="recovery-code">{{ $code }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Disable two-factor authentication</button>
            </form>
        @endif
    </main>
</body>
</html>
