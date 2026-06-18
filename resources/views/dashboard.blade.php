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

        .dashboard-section {
            border-top: 1px solid #e6e8ef;
            margin-top: 28px;
            padding-top: 24px;
        }

        .dashboard-section__header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .dashboard-section h2 {
            font-size: 20px;
            margin: 0 0 6px;
        }

        .dashboard-link {
            color: #2447f9;
            font-weight: 700;
            text-decoration: none;
        }

        .dashboard-link:hover {
            text-decoration: underline;
        }

        .page-hierarchy {
            display: grid;
            gap: 8px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .page-hierarchy__item {
            align-items: center;
            border-bottom: 1px solid #edf0f5;
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 1fr) auto auto;
            padding: 11px 0 11px calc(var(--page-depth) * 22px);
        }

        .page-hierarchy__item:last-child {
            border-bottom: 0;
        }

        .page-hierarchy__title {
            align-items: center;
            display: flex;
            gap: 8px;
            min-width: 0;
        }

        .page-hierarchy__branch {
            color: #9aa4b2;
            font-weight: 700;
        }

        .page-hierarchy__title strong {
            overflow-wrap: anywhere;
        }

        .page-hierarchy__meta {
            color: #526071;
            display: block;
            font-size: 13px;
            margin-top: 3px;
            overflow-wrap: anywhere;
        }

        .page-status {
            border: 1px solid #cfd6e3;
            border-radius: 999px;
            color: #526071;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 9px;
            white-space: nowrap;
        }

        .page-status--published {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .empty-state {
            border: 1px dashed #cfd6e3;
            border-radius: 8px;
            color: #526071;
            padding: 16px;
        }

        @media (max-width: 640px) {
            .dashboard-section__header,
            .page-hierarchy__item {
                grid-template-columns: 1fr;
            }

            .dashboard-section__header {
                display: grid;
            }

            .page-actions {
                justify-content: flex-start;
            }
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

                @can('pages.view')
                    <a class="dashboard-action" href="{{ route('dashboard.cms.pages.index') }}">
                        <strong>Pages</strong>
                        <span>Create CMS pages with paragraph templates.</span>
                    </a>
                @endcan

                <a class="dashboard-action" href="{{ route('two-factor.show') }}">
                    <strong>Security</strong>
                    <span>Manage your two-factor authentication setup.</span>
                </a>
            </div>

            @can('pages.view')
                <section class="dashboard-section" aria-labelledby="dashboard-pages-heading">
                    <div class="dashboard-section__header">
                        <div>
                            <h2 id="dashboard-pages-heading">Created pages</h2>
                            <p>Current CMS pages arranged by parent hierarchy.</p>
                        </div>

                        <a class="dashboard-link" href="{{ route('dashboard.cms.pages.index') }}">Manage pages</a>
                    </div>

                    @if ($pageHierarchy->isNotEmpty())
                        <ul class="page-hierarchy">
                            @foreach ($pageHierarchy as $item)
                                @php
                                    $page = $item['page'];
                                    $depth = min($item['depth'], 6);
                                @endphp

                                <li class="page-hierarchy__item" style="--page-depth: {{ $depth }};">
                                    <div>
                                        <span class="page-hierarchy__title">
                                            @if ($depth > 0)
                                                <span class="page-hierarchy__branch" aria-hidden="true">&rdsh;</span>
                                            @endif
                                            <strong>{{ $page->title }}</strong>
                                        </span>
                                        <span class="page-hierarchy__meta">/{{ $page->slug }}</span>
                                    </div>

                                    <span class="page-status {{ $page->is_published ? 'page-status--published' : '' }}">
                                        {{ $page->is_published ? 'Published' : 'Draft' }}
                                    </span>

                                    <div class="page-actions">
                                        @if ($depth === 0)
                                            @can('pages.create')
                                                <a class="dashboard-link" href="{{ route('dashboard.cms.pages.create', ['parent_id' => $page->id]) }}">Create sub-page</a>
                                            @endcan
                                        @endif

                                        @can('pages.update')
                                            <a class="dashboard-link" href="{{ route('dashboard.cms.pages.edit', $page) }}">Edit page</a>
                                            <a class="dashboard-link" href="{{ route('dashboard.cms.pages.edit', $page) }}#paragraphs">Add paragraph</a>
                                        @endcan
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state">No pages have been created yet.</div>
                    @endif
                </section>
            @endcan
        </section>
    </main>
</body>
</html>
