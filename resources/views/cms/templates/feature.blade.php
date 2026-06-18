<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - Universe</title>
    @include('cms.templates.styles')
</head>
<body class="cms-page has-universe-topbar">
    @include('components.topbar')

    <main class="cms-shell cms-shell--feature">
        <header class="cms-hero">
            <h1>{{ $page->title }}</h1>
        </header>

        @foreach ($page->paragraphs as $paragraph)
            <section class="cms-feature-row {{ $loop->even ? 'cms-feature-row--reverse' : '' }}">
                @if ($paragraph->image_path)
                    <img src="{{ Storage::url($paragraph->image_path) }}" alt="">
                @endif
                <div>
                    <p class="cms-kicker">{{ $paragraph->subheading }}</p>
                    <h2>{{ $paragraph->heading }}</h2>
                    <div class="cms-rich-text">
                        {!! \App\Support\CmsHtmlSanitizer::sanitize($paragraph->body) !!}
                    </div>
                </div>
            </section>
        @endforeach
    </main>
</body>
</html>
