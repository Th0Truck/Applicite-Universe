@props([
    'currentPage',
    'subPages',
    'topLevelPage',
])

@if ($subPages->isNotEmpty())
    <footer class="cms-footer" aria-labelledby="cms-footer-heading">
        <div class="cms-footer__header">
            <p class="cms-footer__eyebrow">Explore</p>
            <h2 id="cms-footer-heading">{{ $topLevelPage->title }}</h2>
        </div>
        <nav class="cms-footer__nav" aria-label="{{ $topLevelPage->title }} sub-pages">
            @foreach ($subPages as $subPage)
                <a
                    class="cms-footer__link {{ $currentPage->is($subPage) ? 'cms-footer__link--current' : '' }}"
                    href="{{ route('cms.pages.show', $subPage->slug) }}"
                    @if ($currentPage->is($subPage)) aria-current="page" @endif
                >
                    {{ $subPage->title }}
                </a>
            @endforeach
        </nav>
    </footer>
@endif
