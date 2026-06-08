@php
    $dashboardUrl = route('dashboard');
    $currentUser = Auth::user();
    $accountLabel = $currentUser?->name ?? $currentUser?->email;
@endphp

<style>
    html.universe-theme-dark body {
        background: #111827 !important;
        color: #e5e7eb !important;
    }

    html.universe-theme-dark .auth-container,
    html.universe-theme-dark .setup-container,
    html.universe-theme-dark .dashboard-panel,
    html.universe-theme-dark .admin-panel {
        background: #172033 !important;
        border-color: #2f3c52 !important;
        color: #e5e7eb !important;
    }

    html.universe-theme-dark .text-muted,
    html.universe-theme-dark .dashboard-panel p,
    html.universe-theme-dark .admin-header p,
    html.universe-theme-dark .form-help {
        color: #a9b4c6 !important;
    }

    body.has-universe-topbar {
        padding-top: 88px !important;
    }

    .universe-topbar {
        background: rgba(255, 255, 255, 0.96);
        border-bottom: 1px solid #e6e8ef;
        box-shadow: 0 8px 22px rgba(20, 24, 40, 0.08);
        color: #172033;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        left: 0;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 50;
    }

    .universe-topbar * {
        box-sizing: border-box;
    }

    .universe-topbar__inner {
        align-items: center;
        display: flex;
        gap: 20px;
        justify-content: space-between;
        margin: 0 auto;
        max-width: 1180px;
        min-height: 64px;
        padding: 0 20px;
    }

    .universe-topbar__brand {
        color: #172033;
        font-size: 18px;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
    }

    .universe-topbar__toggle {
        display: none;
    }

    .universe-topbar__toggle-button {
        align-items: center;
        background: #f5f7fb;
        border: 1px solid #d8deea;
        border-radius: 6px;
        cursor: pointer;
        display: none;
        height: 40px;
        justify-content: center;
        width: 44px;
    }

    .universe-topbar__toggle-button span,
    .universe-topbar__toggle-button span::before,
    .universe-topbar__toggle-button span::after {
        background: #172033;
        border-radius: 999px;
        content: "";
        display: block;
        height: 2px;
        position: absolute;
        width: 18px;
    }

    .universe-topbar__toggle-button span {
        position: relative;
    }

    .universe-topbar__toggle-button span::before {
        top: -6px;
    }

    .universe-topbar__toggle-button span::after {
        top: 6px;
    }

    .universe-topbar__menu {
        align-items: center;
        display: flex;
        gap: 10px;
    }

    .universe-topbar__user {
        color: #526071;
        font-size: 14px;
        margin-right: 6px;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .universe-topbar__link,
    .universe-topbar__button {
        align-items: center;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 6px;
        color: #172033;
        cursor: pointer;
        display: inline-flex;
        font-size: 14px;
        font-weight: 600;
        min-height: 38px;
        padding: 8px 12px;
        text-decoration: none;
        white-space: nowrap;
    }

    .universe-topbar__link:hover,
    .universe-topbar__button:hover {
        background: #f5f7fb;
        border-color: #d8deea;
        color: #172033;
        text-decoration: none;
    }

    .universe-topbar__link--primary {
        background: #2447f9;
        border-color: #2447f9;
        color: #fff;
    }

    .universe-topbar__link--primary:hover {
        background: #1939da;
        border-color: #1939da;
        color: #fff;
    }

    .universe-topbar__logout {
        margin: 0;
    }

    .universe-topbar__account {
        position: relative;
    }

    .universe-topbar__account summary {
        list-style: none;
    }

    .universe-topbar__account summary::-webkit-details-marker {
        display: none;
    }

    .universe-topbar__account-button {
        gap: 8px;
        max-width: 240px;
    }

    .universe-topbar__account-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .universe-topbar__account-caret {
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-top: 5px solid currentColor;
        flex: 0 0 auto;
        height: 0;
        width: 0;
    }

    .universe-topbar__dropdown {
        background: white;
        border: 1px solid #d8deea;
        border-radius: 8px;
        box-shadow: 0 18px 34px rgba(20, 24, 40, 0.16);
        min-width: 240px;
        padding: 8px;
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        z-index: 60;
    }

    .universe-topbar__dropdown-header {
        border-bottom: 1px solid #edf0f5;
        color: #526071;
        font-size: 13px;
        margin-bottom: 6px;
        overflow: hidden;
        padding: 8px 10px 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .universe-topbar__dropdown-item {
        align-items: center;
        background: transparent;
        border: 0;
        border-radius: 6px;
        color: #172033;
        cursor: pointer;
        display: flex;
        font: inherit;
        font-size: 14px;
        font-weight: 600;
        justify-content: space-between;
        min-height: 38px;
        padding: 9px 10px;
        text-align: left;
        text-decoration: none;
        width: 100%;
    }

    .universe-topbar__dropdown-item:hover {
        background: #f5f7fb;
        color: #172033;
        text-decoration: none;
    }

    .universe-topbar__dropdown-form {
        margin: 0;
    }

    html.universe-theme-dark .universe-topbar {
        background: rgba(23, 32, 51, 0.96);
        border-bottom-color: #2f3c52;
        color: #e5e7eb;
    }

    html.universe-theme-dark .universe-topbar__brand,
    html.universe-theme-dark .universe-topbar__link,
    html.universe-theme-dark .universe-topbar__button,
    html.universe-theme-dark .universe-topbar__dropdown-item {
        color: #e5e7eb;
    }

    html.universe-theme-dark .universe-topbar__link:hover,
    html.universe-theme-dark .universe-topbar__button:hover,
    html.universe-theme-dark .universe-topbar__dropdown-item:hover,
    html.universe-theme-dark .universe-topbar__toggle-button {
        background: #22304a;
        border-color: #3d4b64;
        color: #e5e7eb;
    }

    html.universe-theme-dark .universe-topbar__dropdown {
        background: #172033;
        border-color: #3d4b64;
    }

    html.universe-theme-dark .universe-topbar__dropdown-header {
        border-bottom-color: #2f3c52;
        color: #a9b4c6;
    }

    html.universe-theme-dark .universe-topbar__toggle-button span,
    html.universe-theme-dark .universe-topbar__toggle-button span::before,
    html.universe-theme-dark .universe-topbar__toggle-button span::after {
        background: #e5e7eb;
    }

    @media (max-width: 720px) {
        body.has-universe-topbar {
            padding-top: 76px !important;
        }

        .universe-topbar__inner {
            flex-wrap: wrap;
            min-height: 56px;
            padding: 0 14px;
        }

        .universe-topbar__toggle-button {
            display: inline-flex;
        }

        .universe-topbar__menu {
            align-items: stretch;
            display: none;
            flex-basis: 100%;
            flex-direction: column;
            gap: 8px;
            padding: 0 0 14px;
        }

        .universe-topbar__toggle:checked ~ .universe-topbar__menu {
            display: flex;
        }

        .universe-topbar__link,
        .universe-topbar__button {
            justify-content: center;
            width: 100%;
        }

        .universe-topbar__account {
            width: 100%;
        }

        .universe-topbar__account-button {
            justify-content: center;
            max-width: none;
            width: 100%;
        }

        .universe-topbar__dropdown {
            box-shadow: none;
            margin-top: 8px;
            position: static;
            width: 100%;
        }
    }
</style>

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
            @auth
                <a class="universe-topbar__link {{ request()->routeIs('dashboard') ? 'universe-topbar__link--primary' : '' }}" href="{{ $dashboardUrl }}">
                    Dashboard
                </a>
                <a class="universe-topbar__link" href="{{ route('two-factor.show') }}">
                    Security
                </a>
                @can('users.view')
                    <a class="universe-topbar__link {{ request()->routeIs('dashboard.users.*') ? 'universe-topbar__link--primary' : '' }}" href="{{ route('dashboard.users.index') }}">
                        Users
                    </a>
                @endcan
                @can('pages.view')
                    <a class="universe-topbar__link {{ request()->routeIs('dashboard.cms.*') ? 'universe-topbar__link--primary' : '' }}" href="{{ route('dashboard.cms.pages.index') }}">
                        Pages
                    </a>
                @endcan
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

                        <a class="universe-topbar__dropdown-item" href="{{ route('two-factor.show') }}">
                            Security
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
