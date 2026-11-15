<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galleries - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="dashboard-shell">
        <section class="dashboard-panel">
            <x-dashboard-menu />
            <div class="admin-header">
                <div>
                    <h1>Galleries</h1>
                    <p>Upload images and attach them to frontend galleries.</p>
                </div>
                @can('galleries.create')
                    <a class="button" href="{{ route('dashboard.galleries.create') }}">Create gallery</a>
                @endcan
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Images</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($galleries as $gallery)
                        <tr>
                            <td>
                                <strong>{{ $gallery->title }}</strong>
                                <span class="muted">/{{ $gallery->slug }}</span>
                            </td>
                            <td>{{ $gallery->is_published ? 'Published' : 'Draft' }}</td>
                            <td>{{ $gallery->images_count }}</td>
                            <td class="actions-cell">
                                @if ($gallery->is_published)
                                    <a class="admin-link" href="{{ route('galleries.show', $gallery->slug) }}">View</a>
                                @endif

                                @can('galleries.update')
                                    <a class="admin-link" href="{{ route('dashboard.galleries.edit', $gallery) }}">Edit</a>
                                @endcan

                                @can('galleries.delete')
                                    <form method="POST" action="{{ route('dashboard.galleries.destroy', $gallery) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-button" type="submit">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No galleries have been created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-wrap">
                {{ $galleries->links() }}
            </div>
            <a class="admin-link" href="{{ route('dashboard') }}">Back to dashboard</a>
        </section>

        <x-dashboard-footer />
    </main>
</body>
</html>
