<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gallery - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <x-dashboard-menu />

            <div class="admin-header">
                <div>
                    <h1>Edit gallery</h1>
                    <p>{{ $gallery->title }}</p>
                </div>
                <a class="admin-link" href="{{ route('dashboard.galleries.index') }}">Back to galleries</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @include('dashboard.cms.pages.errors')

            <form method="POST" action="{{ route('dashboard.galleries.update', $gallery) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('dashboard.galleries._form', ['buttonLabel' => 'Save gallery'])
            </form>
        </section>

        <x-dashboard-footer />
    </main>
</body>
</html>
