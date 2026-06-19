@props([
    'hex',
    'name',
    'token',
])

<article class="token-card">
    <span class="color-swatch" style="--swatch-color: {{ $hex }};"></span>
    <div>
        <strong>{{ $name }}</strong>
        <code>{{ $token }}</code>
        <span>{{ $hex }}</span>
    </div>
</article>
