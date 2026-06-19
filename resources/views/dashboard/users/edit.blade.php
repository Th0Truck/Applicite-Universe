<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell admin-shell--narrow">
        <section class="admin-panel">
            <x-dashboard-menu />

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

        <x-dashboard-footer />
    </main>
</body>
</html>
