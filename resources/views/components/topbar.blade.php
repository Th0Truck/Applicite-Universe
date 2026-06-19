@php
    $currentUser = Auth::user();
    $accountLabel = $currentUser?->name ?? $currentUser?->email;
    $currentPageSlug = request()->route('slug');
    $topbarPages = $topbarPages ?? collect();
@endphp

@once
    @vite('resources/css/app.css')
@endonce

<nav class="universe-topbar" aria-label="Primary navigation">
    <div class="universe-topbar__inner">
        <a class="universe-topbar__brand" href="{{ url('/') }}">
            {{ config('app.name', 'Universe') }}
        </a>

        <input class="universe-topbar__toggle" type="checkbox" id="universe-topbar-toggle">
        <label class="universe-topbar__toggle-button" for="universe-topbar-toggle" aria-label="Toggle navigation">
            <span></span>
        </label>

        <div class="universe-topbar__menu">
            @foreach ($topbarPages as $topbarPage)
                @php
                    $childPages = $topbarPage->children;
                    $isCurrentPage = $currentPageSlug === $topbarPage->slug || $childPages->contains('slug', $currentPageSlug);
                @endphp

                @if ($childPages->isNotEmpty())
                    <details class="universe-topbar__page">
                        <summary class="universe-topbar__button universe-topbar__page-button {{ $isCurrentPage ? 'universe-topbar__link--primary' : '' }}">
                            <span>{{ $topbarPage->title }}</span>
                            <span class="universe-topbar__account-caret" aria-hidden="true"></span>
                        </summary>

                        <div class="universe-topbar__dropdown">
                            <a class="universe-topbar__dropdown-item" href="{{ route('cms.pages.show', $topbarPage->slug) }}">
                                {{ $topbarPage->title }}
                                <span></span>
                            </a>

                            @foreach ($childPages as $childPage)
                                <a class="universe-topbar__dropdown-item" href="{{ route('cms.pages.show', $childPage->slug) }}">
                                    {{ $childPage->title }}
                                    <span></span>
                                </a>
                            @endforeach
                        </div>
                    </details>
                @else
                    <a class="universe-topbar__link {{ $isCurrentPage ? 'universe-topbar__link--primary' : '' }}" href="{{ route('cms.pages.show', $topbarPage->slug) }}">
                        {{ $topbarPage->title }}
                    </a>
                @endif
            @endforeach

            @auth
                <details class="universe-topbar__account">
                    <summary class="universe-topbar__button universe-topbar__account-button">
                        <span class="universe-topbar__account-name">{{ $accountLabel }}</span>
                        <span class="universe-topbar__account-caret" aria-hidden="true"></span>
                    </summary>

                    <div class="universe-topbar__dropdown">
                        <div class="universe-topbar__dropdown-header">{{ $currentUser->email }}</div>

                        <button class="universe-topbar__dropdown-item" type="button" data-theme-toggle aria-pressed="false">
                            <span>Appearance</span>
                            <span data-theme-label>Light</span>
                        </button>
                        <a class="universe-topbar__dropdown-item" href="{{ route('dashboard') }}">
                            Dashboard
                            <span></span>
                        </a>

                        <form class="universe-topbar__dropdown-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="universe-topbar__dropdown-item" type="submit">
                                Logout
                                <span></span>
                            </button>
                        </form>
                    </div>
                </details>
            @else
                <a class="universe-topbar__link" href="{{ route('login') }}">Login</a>
                <a class="universe-topbar__link universe-topbar__link--primary" href="{{ route('register') }}">Sign up</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    (function () {
        var storageKey = 'universe-theme';
        var root = document.documentElement;
        var storedTheme = localStorage.getItem(storageKey);
        var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        var initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');

        function applyTheme(theme) {
            root.classList.toggle('universe-theme-dark', theme === 'dark');
            document.querySelectorAll('[data-theme-toggle]').forEach(function (button) {
                button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
                button.querySelector('[data-theme-label]').textContent = theme === 'dark' ? 'Dark' : 'Light';
            });
        }

        applyTheme(initialTheme);

        document.addEventListener('click', function (event) {
            var button = event.target.closest('[data-theme-toggle]');

            if (! button) {
                return;
            }

            var nextTheme = root.classList.contains('universe-theme-dark') ? 'light' : 'dark';

            localStorage.setItem(storageKey, nextTheme);
            applyTheme(nextTheme);
        });
    })();
</script>
