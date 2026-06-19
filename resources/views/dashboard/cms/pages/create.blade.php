<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Page - Universe</title>
    @vite('resources/css/dashboard.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main class="admin-shell">
        <section class="admin-panel">
            <x-dashboard-menu />

            <div class="admin-header">
                <div>
                    <h1>Create page</h1>
                    <p>Build a CMS page from paragraph blocks.</p>
                </div>
                <a class="admin-link" href="{{ route('dashboard.cms.pages.index') }}">Back to pages</a>
            </div>

            @include('dashboard.cms.pages.errors')

            <form method="POST" action="{{ route('dashboard.cms.pages.store') }}" enctype="multipart/form-data">
                @csrf
                @include('dashboard.cms.pages._form', ['buttonLabel' => 'Create page'])
            </form>
        </section>

        <x-dashboard-footer />
    </main>
</body>
</html>
