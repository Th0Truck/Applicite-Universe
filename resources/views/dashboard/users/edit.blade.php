<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Universe</title>
    <style>
        body {
            background: #f6f7fb;
            color: #172033;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        .admin-shell {
            margin: 0 auto;
            max-width: 860px;
            padding: 32px 20px;
        }

        .admin-panel {
            background: white;
            border: 1px solid #e6e8ef;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
            padding: 28px;
        }

        .admin-header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .admin-header h1 {
            font-size: 24px;
            margin: 0 0 6px;
        }

        .admin-header p,
        .form-help {
            color: #526071;
            margin: 0;
        }

        .admin-link {
            color: #2447f9;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .admin-link:hover {
            text-decoration: underline;
        }

        .form-section {
            border-top: 1px solid #e6e8ef;
            padding-top: 24px;
        }

        .form-section + .form-section {
            margin-top: 28px;
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .form-control {
            border: 1px solid #cfd6e3;
            border-radius: 6px;
            color: #172033;
            font: inherit;
            min-height: 42px;
            padding: 9px 11px;
            width: 100%;
        }

        .role-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 12px;
        }

        .role-option {
            align-items: center;
            border: 1px solid #d8deea;
            border-radius: 6px;
            display: flex;
            gap: 10px;
            padding: 12px;
        }

        .button-row {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        .button {
            background: #2447f9;
            border: 1px solid #2447f9;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
            min-height: 42px;
            padding: 9px 14px;
        }

        .alert {
            border-radius: 6px;
            margin-bottom: 18px;
            padding: 12px 14px;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        @media (max-width: 720px) {
            .admin-header {
                flex-direction: column;
            }
        }
    </style>
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <div class="admin-header">
                <div>
                    <h1>Edit user</h1>
                    <p>{{ $user->email }}</p>
                </div>
                <a class="admin-link" href="{{ route('dashboard.users.index') }}">Back to users</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="form-section" method="POST" action="{{ route('dashboard.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="button-row">
                    <button class="button" type="submit">Save user</button>
                </div>
            </form>

            @can('roles.manage')
                <form class="form-section" method="POST" action="{{ route('dashboard.users.roles.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <h2>Rights</h2>
                    <p class="form-help">Assign roles to control what this user can access.</p>

                    <div class="role-grid">
                        @foreach ($roles as $role)
                            <label class="role-option">
                                <input
                                    type="checkbox"
                                    name="roles[]"
                                    value="{{ $role->name }}"
                                    @checked($user->hasRole($role->name))
                                >
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="button-row">
                        <button class="button" type="submit">Save rights</button>
                    </div>
                </form>
            @endcan
        </section>
    </main>
</body>
</html>
