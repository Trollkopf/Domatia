<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-uppercase" href="{{ url('/') }}">
            {{ $siteSettings['company_name'] }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">{{ __('ui.nav.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guest.properties.index') }}">{{ __('ui.nav.properties') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">{{ __('ui.nav.about') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('environment') }}">{{ __('ui.nav.environment') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">{{ __('ui.nav.contact') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-inline-flex align-items-center gap-2" href="{{ route('guest.properties.favorites') }}" data-favorites-link>
                        <span>{{ __('ui.nav.favorites') }}</span>
                        <span class="badge rounded-pill text-bg-dark {{ ($favoritePropertiesCount ?? 0) > 0 ? '' : 'd-none' }}" data-favorites-count>
                            {{ $favoritePropertiesCount ?? 0 }}
                        </span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <button class="nav-link dropdown-toggle border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ strtoupper($currentLocale ?? app()->getLocale()) }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach (($supportedLocales ?? []) as $localeCode => $localeLabel)
                            <li>
                                <a class="dropdown-item {{ ($currentLocale ?? app()->getLocale()) === $localeCode ? 'active' : '' }}"
                                   href="{{ route('locale.switch', ['locale' => $localeCode, 'redirect' => url()->full()]) }}">
                                    {{ $localeLabel }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
