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

        body.admin-nav-open {
            overflow: hidden;
        }

        .admin-shell {
            min-height: 100vh;
            min-height: 100dvh;
        }

        .admin-sidebar {
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            color: #fff;
            padding: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
            height: 100dvh;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-self: start;
            z-index: 1040;
            scrollbar-width: thin;
        }

        .admin-sidebar-desktop {
            position: fixed;
            inset: 0 auto 0 0;
            width: 280px;
        }

        .admin-sidebar-mobile {
            position: fixed;
            inset: 0 auto 0 0;
            width: min(320px, calc(100vw - 2rem));
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            z-index: 1050;
            border-radius: 0 24px 24px 0;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
        }

        .admin-sidebar-mobile.open {
            transform: translateX(0);
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
            margin-top: auto;
            padding-top: 1.5rem;
            padding-bottom: 0.25rem;
        }

        .sidebar-footer-inner {
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
            margin-left: 280px;
            padding: 1.5rem;
        }

        .admin-topbar {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--admin-border);
            border-radius: 20px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            position: sticky;
            top: 1rem;
            z-index: 1020;
        }

        .content-surface {
            min-width: 0;
        }

        .admin-sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.52);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            z-index: 1030;
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
            .admin-sidebar-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }

            .admin-main {
                margin-left: 0;
                padding: 1rem;
            }

            .admin-topbar {
                top: 0.75rem;
                padding: 0.9rem 1rem;
            }
        }
    </style>
</head>

<body>
    @php
        $currentRoute = request()->route()?->getName();
        $adminUser = auth()->user();
        $hasCommercialAccess = $adminUser?->canManageProperties()
            || $adminUser?->canManageContacts()
            || $adminUser?->canManageZonas();

        $generalLinks = [
            [
                'visible' => true,
                'url' => route('admin.dashboard'),
                'icon' => 'fas fa-house',
                'label' => 'Dashboard',
                'active' => $currentRoute === 'admin.dashboard',
            ],
        ];

        $commercialLinks = [
            [
                'visible' => $adminUser?->canManageProperties(),
                'url' => route('admin.properties.index'),
                'icon' => 'fas fa-building',
                'label' => 'Propiedades',
                'active' => str_starts_with($currentRoute ?? '', 'admin.properties.'),
            ],
            [
                'visible' => $adminUser?->canManageProperties(),
                'url' => route('admin.propietarios.index'),
                'icon' => 'fas fa-user-tie',
                'label' => 'Propietarios',
                'active' => str_starts_with($currentRoute ?? '', 'admin.propietarios.'),
            ],
            [
                'visible' => $adminUser?->canManageProperties(),
                'url' => route('admin.kyero.index'),
                'icon' => 'fas fa-file-import',
                'label' => 'Importar Kyero',
                'active' => str_starts_with($currentRoute ?? '', 'admin.kyero.'),
            ],
            [
                'visible' => $adminUser?->canManageContacts(),
                'url' => route('admin.contactos.index'),
                'icon' => 'fas fa-address-book',
                'label' => 'Contactos',
                'active' => str_starts_with($currentRoute ?? '', 'admin.contactos.'),
            ],
            [
                'visible' => $adminUser?->canManageZonas(),
                'url' => route('admin.zonas.index'),
                'icon' => 'fas fa-map-location-dot',
                'label' => 'Zonas',
                'active' => str_starts_with($currentRoute ?? '', 'admin.zonas.'),
            ],
        ];

        $adminLinks = [
            [
                'visible' => $adminUser?->canManageUsers(),
                'url' => route('admin.users.index'),
                'icon' => 'fas fa-users',
                'label' => 'Usuarios',
                'active' => str_starts_with($currentRoute ?? '', 'admin.users.'),
            ],
            [
                'visible' => true,
                'url' => route('profile.edit'),
                'icon' => 'fas fa-id-card',
                'label' => 'Mi perfil',
                'active' => str_starts_with($currentRoute ?? '', 'profile.'),
            ],
            [
                'visible' => $adminUser?->canManageSettings(),
                'url' => route('admin.settings'),
                'icon' => 'fas fa-sliders',
                'label' => 'Ajustes',
                'active' => str_starts_with($currentRoute ?? '', 'admin.settings'),
            ],
            [
                'visible' => $adminUser?->canViewReports(),
                'url' => route('admin.reports'),
                'icon' => 'fas fa-chart-column',
                'label' => 'Informes',
                'active' => $currentRoute === 'admin.reports',
            ],
        ];
    @endphp
    @php
            $renderAdminSidebarGroup = static function (string $label, array $links): string {
                $visibleLinks = array_filter($links, fn ($link) => $link['visible']);

                if ($visibleLinks === []) {
                    return '';
                }

                $html = '<div class="sidebar-group">';
                $html .= '<div class="sidebar-label">' . e($label) . '</div>';

                foreach ($visibleLinks as $link) {
                    $html .= '<a href="' . e($link['url']) . '" class="sidebar-link' . ($link['active'] ? ' active' : '') . '">';
                    $html .= '<i class="' . e($link['icon']) . '"></i>';
                    $html .= '<span>' . e($link['label']) . '</span>';
                    $html .= '</a>';
                }

                $html .= '</div>';

                return $html;
            };
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar admin-sidebar-desktop d-none d-lg-flex">
            <div class="brand-panel">
                <div class="text-center">
                    <img src="{{ asset('images/domatia_logo.png') }}" alt="Domatia Logo" class="img-fluid mx-auto d-block">
                </div>
                <div class="small text-center text-white-50 mt-3">Centro de control</div>
            </div>

            {!! $renderAdminSidebarGroup('General', $generalLinks) !!}
            @if ($hasCommercialAccess)
                {!! $renderAdminSidebarGroup('Comercial', $commercialLinks) !!}
            @endif
            {!! $renderAdminSidebarGroup('Administracion', $adminLinks) !!}

            <div class="sidebar-footer">
                <div class="sidebar-footer-inner">
                    <a href="{{ url('/') }}" class="sidebar-link sidebar-link-muted" target="_blank">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Ver web pública</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="sidebar-link logout-button">
                            <i class="fas fa-right-from-bracket"></i>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <aside class="admin-sidebar admin-sidebar-mobile d-lg-none" id="admin-sidebar-mobile" aria-hidden="true">
            <div class="brand-panel">
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <div class="text-center flex-grow-1">
                        <img src="{{ asset('images/domatia_logo.png') }}" alt="Domatia Logo" class="img-fluid mx-auto d-block">
                        <div class="small text-center text-white-50 mt-3">Centro de control</div>
                    </div>
                    <button id="menu-close" class="btn btn-outline-light btn-sm align-self-start" type="button" aria-label="Cerrar menu">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
            </div>

            {!! $renderAdminSidebarGroup('General', $generalLinks) !!}
            @if ($hasCommercialAccess)
                {!! $renderAdminSidebarGroup('Comercial', $commercialLinks) !!}
            @endif
            {!! $renderAdminSidebarGroup('Administracion', $adminLinks) !!}

            <div class="sidebar-footer">
                <div class="sidebar-footer-inner">
                    <a href="{{ url('/') }}" class="sidebar-link sidebar-link-muted" target="_blank">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                        <span>Ver web pública</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="sidebar-link logout-button">
                            <i class="fas fa-right-from-bracket"></i>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <button class="admin-sidebar-backdrop" id="admin-sidebar-backdrop" type="button" aria-label="Cerrar menu"></button>

        <main class="admin-main">
            <div class="admin-topbar d-flex justify-content-between align-items-center gap-3">
                <div>
                    <div class="small text-uppercase text-muted fw-semibold">Backoffice</div>
                    <div class="fw-semibold">@yield('title', 'Panel')</div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted d-none d-md-inline">{{ $adminUser?->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-dark btn-sm d-none d-md-inline-flex">Mi perfil</a>
                    @if ($adminUser?->canManageContacts())
                        <a href="{{ route('admin.contactos.index', ['status' => 'pendiente']) }}" class="btn btn-outline-dark btn-sm d-none d-md-inline-flex">Contactos pendientes</a>
                    @endif
                    @if ($adminUser?->canViewReports())
                        <a href="{{ route('admin.reports') }}" class="btn btn-outline-dark btn-sm d-none d-md-inline-flex">Informes</a>
                    @endif
                    @if ($adminUser?->canManageUsers())
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-dark btn-sm d-none d-md-inline-flex">Nuevo usuario</a>
                    @endif
                    @if ($adminUser?->canManageProperties())
                        <a href="{{ route('admin.properties.create') }}" class="btn btn-main btn-sm d-none d-md-inline-flex">Nueva propiedad</a>
                    @endif
                    <button id="menu-toggle" class="btn btn-outline-dark d-lg-none" type="button" aria-label="Abrir menu" aria-controls="admin-sidebar" aria-expanded="false">
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
            const sidebar = document.getElementById('admin-sidebar-mobile');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const closeButton = document.getElementById('menu-close');

            const setOpenState = function (isOpen) {
                if (!sidebar || !toggle || !backdrop) {
                    return;
                }

                sidebar.classList.toggle('open', isOpen);
                backdrop.classList.toggle('open', isOpen);
                document.body.classList.toggle('admin-nav-open', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                sidebar.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            };

            if (toggle && sidebar && backdrop) {
                toggle.addEventListener('click', function () {
                    const shouldOpen = !sidebar.classList.contains('open');
                    setOpenState(shouldOpen);
                });

                if (closeButton) {
                    closeButton.addEventListener('click', function () {
                        setOpenState(false);
                    });
                }

                backdrop.addEventListener('click', function () {
                    setOpenState(false);
                });

                sidebar.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        setOpenState(false);
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setOpenState(false);
                    }
                });

                setOpenState(false);
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
