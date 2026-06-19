<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <x-dashboard-menu />

            <div class="admin-header">
                <div>
                    <h1>Edit page</h1>
                    <p>{{ $page->title }}</p>
                </div>
                <a class="admin-link" href="{{ route('dashboard.cms.pages.index') }}">Back to pages</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @include('dashboard.cms.pages.errors')

            <form method="POST" action="{{ route('dashboard.cms.pages.update', $page) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('dashboard.cms.pages._form', ['buttonLabel' => 'Save page'])
            </form>
        </section>

        <x-dashboard-footer />
    </main>
</body>
</html>
