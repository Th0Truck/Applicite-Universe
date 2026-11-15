<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gallery->title }} - Universe</title>
    @vite('resources/css/frontend.css')
</head>
<body class="has-universe-topbar">
    @include('components.topbar')

    <main>
        <h1>{{ $gallery->title }}</h1>

        @if ($gallery->description)
            <p>{{ $gallery->description }}</p>
        @endif

        @foreach ($images as $image)
            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->alt_text }}">
        @endforeach
    </main>
</body>
</html>
