@php
    $dashboardUrl = route('dashboard');
@endphp

<style>
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

        .universe-topbar__user {
            max-width: none;
            padding: 8px 2px;
        }

        .universe-topbar__link,
        .universe-topbar__button {
            justify-content: center;
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
                <span class="universe-topbar__user">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                <form class="universe-topbar__logout" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="universe-topbar__button" type="submit">Logout</button>
                </form>
            @else
                <a class="universe-topbar__link" href="{{ route('login') }}">Login</a>
                <a class="universe-topbar__link universe-topbar__link--primary" href="{{ route('register') }}">Sign up</a>
            @endauth
        </div>
    </div>
</nav>
