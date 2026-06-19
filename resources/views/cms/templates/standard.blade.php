<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - Universe</title>
    @vite('resources/css/frontend.css')
</head>
<body class="cms-page has-universe-topbar">
    @include('components.topbar')

    <main class="cms-shell">
        <header class="cms-hero">
            <h1>{{ $page->title }}</h1>
        </header>

        <div class="cms-content-stack">
            @foreach ($page->paragraphs as $paragraph)
                <article class="cms-block">
                    @if ($paragraph->image_path)
                        <img src="{{ Storage::url($paragraph->image_path) }}" alt="">
                    @endif
                    <p class="cms-kicker">{{ $paragraph->subheading }}</p>
                    <h2>{{ $paragraph->heading }}</h2>
                    <div class="cms-rich-text">
                        {!! \App\Support\CmsHtmlSanitizer::sanitize($paragraph->body) !!}
                    </div>
                </article>
            @endforeach
        </div>

        <x-frontend-page-footer
            :current-page="$page"
            :sub-pages="$footerSubPages"
            :top-level-page="$topLevelPage"
        />
    </main>
</body>
</html>
