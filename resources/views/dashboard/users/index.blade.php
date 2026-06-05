<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Universe</title>
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
            max-width: 1180px;
            padding: 32px 20px;
        }

        .admin-panel {
            background: white;
            border: 1px solid #e6e8ef;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
            overflow: hidden;
        }

        .admin-header {
            align-items: center;
            border-bottom: 1px solid #e6e8ef;
            display: flex;
            justify-content: space-between;
            padding: 22px 24px;
        }

        .admin-header h1 {
            font-size: 24px;
            margin: 0;
        }

        .users-table {
            border-collapse: collapse;
            width: 100%;
        }

        .users-table th,
        .users-table td {
            border-bottom: 1px solid #edf0f5;
            padding: 14px 24px;
            text-align: left;
            vertical-align: middle;
        }

        .users-table th {
            color: #526071;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .role-pill {
            background: #eef2ff;
            border-radius: 999px;
            color: #2447f9;
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            margin: 2px;
            padding: 4px 8px;
        }

        .admin-link {
            color: #2447f9;
            font-weight: 700;
            text-decoration: none;
        }

        .admin-link:hover {
            text-decoration: underline;
        }

        .pagination-wrap {
            padding: 18px 24px;
        }

        @media (max-width: 720px) {
            .admin-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

            .users-table,
            .users-table tbody,
            .users-table tr,
            .users-table td {
                display: block;
                width: 100%;
            }

            .users-table thead {
                display: none;
            }

            .users-table tr {
                border-bottom: 1px solid #edf0f5;
                padding: 14px 0;
            }

            .users-table td {
                border-bottom: 0;
                padding: 7px 24px;
            }
        }
    </style>
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <div class="admin-header">
                <h1>Users</h1>
                <a class="admin-link" href="{{ route('dashboard') }}">Back to dashboard</a>
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
        </section>
    </main>
</body>
</html>
