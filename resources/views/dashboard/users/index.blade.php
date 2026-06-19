<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="dashboard-shell">
        <section class="dashboard-panel">
            <x-dashboard-menu />
            <div class="admin-header">
                <h1>Users</h1>
                
            </div>

            <table class="users-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="role-pill">{{ $role->name }}</span>
                                @empty
                                    <span>No roles</span>
                                @endforelse
                            </td>
                            <td>
                                @can('users.update')
                                    <a class="admin-link" href="{{ route('dashboard.users.edit', $user) }}">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $users->links() }}
            </div>
            <a class="admin-link" href="{{ route('dashboard') }}">Back to dashboard</a>
        </section>

        <x-dashboard-footer />
    </main>
</body>
</html>
