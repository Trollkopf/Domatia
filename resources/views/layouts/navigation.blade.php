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
                    <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('guest.properties.index') }}">Propiedades</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">Nosotros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('environment') }}">Entorno</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">Contacto</a>
                </li>


            </ul>
        </div>
    </div>
</nav>
