<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Universe</title>
    <style>
        body {
            background: #f6f7fb;
            color: #172033;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .dashboard-shell {
            margin: 0 auto;
            max-width: 1180px;
            padding: 32px 20px;
        }

        .dashboard-panel {
            background: white;
            border: 1px solid #e6e8ef;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
            padding: 28px;
        }

        .dashboard-panel h1 {
            font-size: 28px;
            margin: 0 0 8px;
        }

        .dashboard-panel p {
            color: #526071;
            margin: 0;
        }

        .dashboard-actions {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            margin-top: 24px;
        }

        .dashboard-action {
            border: 1px solid #d8deea;
            border-radius: 8px;
            color: #172033;
            display: block;
            padding: 18px;
            text-decoration: none;
        }

        .dashboard-action:hover {
            border-color: #2447f9;
        }

        .dashboard-action strong {
            display: block;
            margin-bottom: 6px;
        }

        .dashboard-action span {
            color: #526071;
            display: block;
            font-size: 14px;
        }
    </style>
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="dashboard-shell">
        <section class="dashboard-panel">
            <h1>Dashboard</h1>
            <p>Welcome back, {{ auth()->user()->name }}.</p>

            <div class="dashboard-actions">
                @can('users.view')
                    <a class="dashboard-action" href="{{ route('dashboard.users.index') }}">
                        <strong>Users</strong>
                        <span>Edit users and review their access rights.</span>
                    </a>
                @endcan

                <a class="dashboard-action" href="{{ route('two-factor.show') }}">
                    <strong>Security</strong>
                    <span>Manage your two-factor authentication setup.</span>
                </a>
            </div>
        </section>
    </main>
</body>
</html>
