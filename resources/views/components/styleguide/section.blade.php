@props([
    'eyebrow' => null,
    'title',
])

<section {{ $attributes->merge(['class' => 'styleguide-section']) }}>
    <div class="styleguide-section__header">
        @if ($eyebrow)
            <p class="styleguide-eyebrow">{{ $eyebrow }}</p>
        @endif

        <h2>{{ $title }}</h2>
    </div>

    {{ $slot }}
</section>
