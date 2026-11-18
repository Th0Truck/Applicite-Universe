<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gallery->title }} - Universe</title>
    @vite('resources/css/frontend.css')
    @vite(['resources/css/gallery.css', 'resources/js/gallery.js'])
</head>
<body class="gallery-page has-universe-topbar">
    @include('components.topbar')

    <main class="gallery">
        <header class="gallery-header">
            <h1>{{ $gallery->title }}</h1>

            @if ($gallery->description)
                <div class="gallery-description">{{ $gallery->description }}</div>
            @endif
        </header>

        <div class="gallery-grid">
            @forelse ($images as $image)
                <figure class="gallery-image">
                    <a class="image-hover-frame" href="{{ Storage::url($image->path) }}"
                       aria-label="{{ $image->alt_text ?: 'View image ' . $loop->iteration }}">
                        <div class="image-hover-border">
                            <span class="image-hover-box" aria-hidden="true"></span>
                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt_text }}"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                        </div>
                    </a>
                </figure>
            @empty
                <p class="gallery-empty">No images have been added to this gallery yet.</p>
            @endforelse
        </div>
    </main>

    @include('galleries._viewer')
</body>
</html>
