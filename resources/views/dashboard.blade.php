<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="dashboard-shell">
        <section class="dashboard-panel">
            <h1>Dashboard</h1>
            <p>Welcome back, {{ auth()->user()->name }}.</p>

            <x-dashboard-menu />

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
