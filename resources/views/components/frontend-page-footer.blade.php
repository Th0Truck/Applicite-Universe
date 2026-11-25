@props([
    'currentPage' => null,
    'subPages' => collect(),
    'topLevelPage' => null,
])

@once
    @vite('resources/js/app.js')
@endonce

<footer class="cms-footer" aria-label="Site footer">
    @if ($topLevelPage !== null && $subPages->isNotEmpty())
        <div class="cms-footer__header">
            <p class="cms-footer__eyebrow">Explore</p>
            <h2>{{ $topLevelPage->title }}</h2>
        </div>
        <nav class="cms-footer__nav" aria-label="{{ $topLevelPage->title }} sub-pages">
            @foreach ($subPages as $subPage)
                <a
                    class="cms-footer__link {{ $currentPage?->is($subPage) ? 'cms-footer__link--current' : '' }}"
                    href="{{ route('cms.pages.show', $subPage->slug) }}"
                    @if ($currentPage?->is($subPage)) aria-current="page" @endif
                >
                    {{ $subPage->title }}
                </a>
            @endforeach
        </nav>
    @endif

    <div class="cms-footer__site-info">
        <p>
            En del af Applicite Universe.
            <a href="https://applicite.com" target="_blank" rel="noopener noreferrer">Læs mere om Applicite</a>.
        </p>
        <p>&copy; {{ date('Y') }} Universe. All rights reserved.</p>
        <ul>
            <li><a href="{{ route('cms.pages.show', 'about') }}">About</a></li>
            <li><a href="{{ route('cms.pages.show', 'privacy-policy') }}">Privacy Policy</a></li>
            <li><a href="{{ route('cms.pages.show', 'terms-of-service') }}">Terms of Service</a></li>
        </ul>
    </div>
</footer>
