<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages - Universe</title>
    @include('dashboard.cms.pages.styles')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <div class="admin-header">
                <div>
                    <h1>Pages</h1>
                    <p>Create and manage CMS pages.</p>
                </div>
                @can('pages.create')
                    <a class="button" href="{{ route('dashboard.cms.pages.create') }}">Create page</a>
                @endcan
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Paragraphs</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td>
                                <strong>{{ $page->title }}</strong>
                                <span class="muted">/{{ $page->slug }}</span>
                            </td>
                            <td>{{ config("cms.templates.{$page->template}.label", $page->template) }}</td>
                            <td>{{ $page->is_published ? 'Published' : 'Draft' }}</td>
                            <td>{{ $page->paragraphs_count }}</td>
                            <td class="actions-cell">
                                @if ($page->is_published)
                                    <a class="admin-link" href="{{ route('cms.pages.show', $page->slug) }}">View</a>
                                @endif

                                @can('pages.update')
                                    <a class="admin-link" href="{{ route('dashboard.cms.pages.edit', $page) }}">Edit</a>
                                @endcan

                                @can('pages.delete')
                                    <form method="POST" action="{{ route('dashboard.cms.pages.destroy', $page) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-button" type="submit">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No pages have been created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $pages->links() }}
            </div>
        </section>
    </main>
</body>
</html>
