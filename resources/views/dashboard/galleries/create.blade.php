<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gallery - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <x-dashboard-menu />

            <div class="admin-header">
                <div>
                    <h1>Create gallery</h1>
                    <p>Upload and attach images for frontend use.</p>
                </div>
                <a class="admin-link" href="{{ route('dashboard.galleries.index') }}">Back to galleries</a>
            </div>

            @include('dashboard.cms.pages.errors')

            <form method="POST" action="{{ route('dashboard.galleries.store') }}" enctype="multipart/form-data">
                @csrf
                @include('dashboard.galleries._form', ['buttonLabel' => 'Create gallery'])
            </form>
        </section>

        <x-dashboard-footer />
    </main>
</body>
</html>
