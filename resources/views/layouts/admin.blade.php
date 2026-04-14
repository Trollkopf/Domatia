<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Backoffice')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')

    <style>
        :root {
            --admin-bg: #f3f4f6;
            --admin-panel: #ffffff;
            --admin-sidebar: #111827;
            --admin-sidebar-muted: #9ca3af;
            --admin-border: #e5e7eb;
            --admin-accent: #d4a52d;
            --admin-text: #111827;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: var(--admin-bg);
            color: var(--admin-text);
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            color: #fff;
            padding: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .brand-panel {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .brand-panel img {
            max-width: 170px;
        }

        .sidebar-group + .sidebar-group {
            margin-top: 1.5rem;
        }

        .sidebar-label {
            color: var(--admin-sidebar-muted);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 0.95rem;
            border-radius: 14px;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar-link i {
            width: 18px;
            text-align: center;
        }

        .sidebar-link-muted {
            color: var(--admin-sidebar-muted);
        }

        .sidebar-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .logout-button {
            width: 100%;
            background: transparent;
            border: 0;
            text-align: left;
            cursor: pointer;
        }

        .admin-main {
            min-width: 0;
            padding: 1.5rem;
        }

        .admin-topbar {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--admin-border);
            border-radius: 20px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        .content-surface {
            min-width: 0;
        }

        .btn-main {
            background-color: var(--admin-accent);
            color: #111827;
            border-radius: 12px;
            font-weight: 600;
            padding: 0.65rem 1rem;
            border: 0;
        }

        .btn-main:hover {
            background-color: #b78d22;
            color: #111827;
        }

        @media (max-width: 991.98px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                z-index: 1050;
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-main {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    @php
        $currentRoute = request()->route()?->getName();
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar">
            <div class="brand-panel">
                <div class="text-center">
                    <img src="{{ asset('images/domatia_logo.png') }}" alt="Domatia Logo" class="img-fluid mx-auto d-block">
                </div>
                <div class="small text-center text-white-50 mt-3">Centro de control del backoffice</div>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-label">General</div>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                    <i class="fas fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-label">Comercial</div>
                <a href="{{ route('admin.properties.index') }}" class="sidebar-link {{ str_starts_with($currentRoute ?? '', 'admin.properties.') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>Propiedades</span>
                </a>
                <a href="{{ route('admin.contactos.index') }}" class="sidebar-link {{ str_starts_with($currentRoute ?? '', 'admin.contactos.') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i>
                    <span>Contactos</span>
                </a>
                <a href="{{ route('admin.zonas.index') }}" class="sidebar-link {{ str_starts_with($currentRoute ?? '', 'admin.zonas.') ? 'active' : '' }}">
                    <i class="fas fa-map-location-dot"></i>
                    <span>Zonas</span>
                </a>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-label">Administracion</div>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ str_starts_with($currentRoute ?? '', 'admin.users.') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="sidebar-link {{ str_starts_with($currentRoute ?? '', 'admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-sliders"></i>
                    <span>Ajustes</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="sidebar-link {{ $currentRoute === 'admin.reports' ? 'active' : '' }}">
                    <i class="fas fa-chart-column"></i>
                    <span>Informes</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <a href="{{ url('/') }}" class="sidebar-link sidebar-link-muted">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    <span>Ver web publica</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="sidebar-link logout-button">
                        <i class="fas fa-right-from-bracket"></i>
                        <span>Cerrar sesion</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-topbar d-flex justify-content-between align-items-center gap-3">
                <div>
                    <div class="small text-uppercase text-muted fw-semibold">Backoffice</div>
                    <div class="fw-semibold">@yield('title', 'Panel')</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.contactos.index', ['status' => 'pendiente']) }}" class="btn btn-outline-dark btn-sm d-none d-md-inline-flex">Leads pendientes</a>
                    <a href="{{ route('admin.properties.create') }}" class="btn btn-main btn-sm d-none d-md-inline-flex">Nueva propiedad</a>
                    <button id="menu-toggle" class="btn btn-outline-dark d-lg-none" type="button" aria-label="Abrir menu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <div class="content-surface">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('admin-sidebar');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                });
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
