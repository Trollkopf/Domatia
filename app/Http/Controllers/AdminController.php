<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Property;
use App\Models\User;
use App\Models\Zona;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::with('userGroup')->get();
        $currentUser = auth()->user();
        $permissions = [
            'properties' => (bool) $currentUser?->canManageProperties(),
            'contacts' => (bool) $currentUser?->canManageContacts(),
            'zonas' => (bool) $currentUser?->canManageZonas(),
            'reports' => (bool) $currentUser?->canViewReports(),
            'users' => (bool) $currentUser?->canManageUsers(),
            'settings' => (bool) $currentUser?->canManageSettings(),
        ];
        $hasAnyPermission = fn (array $keys) => collect($keys)->contains(fn (string $key) => $permissions[$key] ?? false);

        $stats = [
            'properties_total' => Property::count(),
            'properties_featured' => Property::where('is_featured', true)->count(),
            'properties_without_thumbnail' => Property::where(function ($query) {
                $query->whereNull('thumbnail')->orWhere('thumbnail', '');
            })->count(),
            'properties_published' => Property::where('status', 'published')->count(),
            'properties_draft' => Property::where('status', 'draft')->count(),
            'drafts_ready_to_publish' => Property::where('status', 'draft')
                ->whereNotNull('thumbnail')
                ->where('thumbnail', '!=', '')
                ->count(),
            'zonas_total' => Zona::count(),
            'zonas_without_image' => Zona::where(function ($query) {
                $query->whereNull('imagen_principal')->orWhere('imagen_principal', '');
            })->count(),
            'contactos_total' => Contacto::count(),
            'contactos_pendientes' => Contacto::where('status', 'pendiente')->count(),
            'contactos_due' => Contacto::whereNotNull('next_action_at')
                ->whereDate('next_action_at', '<=', now()->toDateString())
                ->count(),
            'contactos_without_next_action' => Contacto::where('status', '!=', 'cerrado')
                ->whereNull('next_action_at')
                ->count(),
            'users_total' => $users->count(),
            'backoffice_users' => $users->filter(fn (User $user) => $user->canAccessBackoffice())->count(),
            'management_users' => $users->filter(fn (User $user) => $user->canManageUsers())->count(),
        ];

        $heroActions = collect([
            [
                'permission' => 'properties',
                'label' => 'Nueva propiedad',
                'url' => route('admin.properties.create'),
                'style' => 'btn-light',
            ],
            [
                'permission' => 'contacts',
                'label' => 'Leads pendientes',
                'url' => route('admin.contactos.index', ['status' => 'pendiente']),
                'style' => 'btn-outline-light',
            ],
            [
                'permission' => 'settings',
                'label' => 'Editar portada y ajustes',
                'url' => route('admin.settings'),
                'style' => 'btn-outline-light',
            ],
            [
                'permission' => 'reports',
                'label' => 'Abrir informes',
                'url' => route('admin.reports'),
                'style' => 'btn-outline-light',
            ],
        ])->filter(fn (array $action) => $permissions[$action['permission']] ?? false)->values()->all();

        $overviewCards = collect([
            [
                'permission' => 'properties',
                'label' => 'Catálogo publicado',
                'value' => $stats['properties_published'],
                'help' => $stats['properties_total'] . ' propiedades en total',
            ],
            [
                'permission' => 'contacts',
                'label' => 'Bandeja comercial',
                'value' => $stats['contactos_pendientes'],
                'help' => $stats['contactos_total'] . ' leads acumulados',
            ],
            [
                'permission' => 'properties',
                'label' => 'Pendientes visuales',
                'value' => $stats['properties_without_thumbnail'],
                'help' => 'propiedades sin imagen principal',
            ],
            [
                'permission' => 'zonas',
                'label' => 'Zonas activas',
                'value' => $stats['zonas_total'],
                'help' => $stats['zonas_without_image'] . ' sin portada principal',
            ],
            [
                'permission' => 'users',
                'label' => 'Equipo y acceso',
                'value' => $stats['users_total'],
                'help' => $stats['management_users'] . ' con gestión y ' . $stats['backoffice_users'] . ' con acceso al backoffice',
            ],
            [
                'permission' => 'reports',
                'label' => 'Alertas operativas',
                'value' => $stats['contactos_without_next_action']
                    + $stats['contactos_due']
                    + $stats['properties_without_thumbnail']
                    + $stats['drafts_ready_to_publish']
                    + $stats['zonas_without_image'],
                'help' => 'elementos a revisar desde los informes',
            ],
        ])->filter(function (array $card) use ($permissions) {
            if ($card['permission'] === 'reports') {
                return $permissions['reports']
                    && ! $permissions['properties']
                    && ! $permissions['contacts']
                    && ! $permissions['zonas']
                    && ! $permissions['users'];
            }

            return $permissions[$card['permission']] ?? false;
        })->values()->all();

        $latestProperties = Property::latest()->take(5)->get();
        $latestContacts = Contacto::with('property')->latest()->take(5)->get();
        $propertiesWithoutThumbnail = Property::where(function ($query) {
            $query->whereNull('thumbnail')->orWhere('thumbnail', '');
        })->latest()->take(5)->get();
        $draftProperties = Property::where('status', 'draft')->latest()->take(5)->get();
        $dueContacts = Contacto::with('property')
            ->whereNotNull('next_action_at')
            ->whereDate('next_action_at', '<=', now()->toDateString())
            ->orderBy('next_action_at')
            ->take(5)
            ->get();

        $propertiesByType = Property::selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->orderByDesc('total')
            ->get();

        $workspaceSections = collect([
            [
                'title' => 'Propiedades',
                'description' => 'Gestión del catálogo, revisión editorial y publicación.',
                'metric' => $stats['properties_total'] . ' registradas',
                'tone' => 'primary',
                'permission' => 'properties',
                'links' => [
                    ['label' => 'Ver catálogo', 'url' => route('admin.properties.index')],
                    ['label' => 'Crear propiedad', 'url' => route('admin.properties.create')],
                    ['label' => 'Borradores', 'url' => route('admin.properties.index', ['status' => 'draft'])],
                ],
            ],
            [
                'title' => 'Contactos',
                'description' => 'Seguimiento comercial y próxima acción de cada lead.',
                'metric' => $stats['contactos_total'] . ' leads',
                'tone' => 'warning',
                'permission' => 'contacts',
                'links' => [
                    ['label' => 'Abrir bandeja', 'url' => route('admin.contactos.index')],
                    ['label' => 'Pendientes', 'url' => route('admin.contactos.index', ['status' => 'pendiente'])],
                    ['label' => 'Seguimientos vencidos', 'url' => route('admin.contactos.index', ['follow_up' => 'due'])],
                ],
            ],
            [
                'title' => 'Zonas',
                'description' => 'Ordena el contenido de entorno y revisa portadas faltantes.',
                'metric' => $stats['zonas_total'] . ' zonas',
                'tone' => 'success',
                'permission' => 'zonas',
                'links' => [
                    ['label' => 'Gestionar zonas', 'url' => route('admin.zonas.index')],
                    ['label' => 'Crear zona', 'url' => route('admin.zonas.create')],
                ],
            ],
            [
                'title' => 'Contenido y ajustes',
                'description' => 'Textos globales, portada y configuración del sitio.',
                'metric' => 'Inicio y páginas editables',
                'tone' => 'dark',
                'permission' => 'settings',
                'links' => [
                    ['label' => 'Abrir ajustes', 'url' => route('admin.settings')],
                    ['label' => 'Ver web pública', 'url' => url('/')],
                ],
            ],
            [
                'title' => 'Equipo',
                'description' => 'Usuarios internos con acceso al backoffice.',
                'metric' => $stats['users_total'] . ' usuarios',
                'tone' => 'info',
                'permission' => 'users',
                'links' => [
                    ['label' => 'Gestionar usuarios', 'url' => route('admin.users.index')],
                ],
            ],
            [
                'title' => 'Informes',
                'description' => 'Visión ejecutiva del rendimiento comercial y del catálogo.',
                'metric' => 'KPI y analitica',
                'tone' => 'secondary',
                'permission' => 'reports',
                'links' => [
                    ['label' => 'Abrir informes', 'url' => route('admin.reports')],
                ],
            ],
        ])->filter(fn (array $section) => $permissions[$section['permission']] ?? false)->values()->all();

        $priorityQueue = collect([
            [
                'permission' => 'contacts',
                'title' => 'Leads sin siguiente accion',
                'count' => $stats['contactos_without_next_action'],
                'help' => 'Contactos abiertos que aún no tienen una fecha de seguimiento.',
                'url' => route('admin.contactos.index'),
                'action' => 'Revisar contactos',
                'empty' => 'Todos los leads abiertos tienen siguiente paso definido.',
                'tone' => 'warning',
            ],
            [
                'permission' => 'contacts',
                'title' => 'Seguimientos vencidos',
                'count' => $stats['contactos_due'],
                'help' => 'Leads que ya deberian haberse trabajado o tocan hoy.',
                'url' => route('admin.contactos.index', ['follow_up' => 'due']),
                'action' => 'Atender ahora',
                'empty' => 'No hay seguimientos vencidos en este momento.',
                'tone' => 'danger',
            ],
            [
                'permission' => 'properties',
                'title' => 'Propiedades sin imagen principal',
                'count' => $stats['properties_without_thumbnail'],
                'help' => 'Fichas que necesitan una portada para verse bien en listados.',
                'url' => route('admin.properties.index', ['missing_thumbnail' => 1]),
                'action' => 'Completar fichas',
                'empty' => 'Todas las propiedades tienen imagen principal.',
                'tone' => 'primary',
            ],
            [
                'permission' => 'properties',
                'title' => 'Borradores listos para publicar',
                'count' => $stats['drafts_ready_to_publish'],
                'help' => 'Propiedades en borrador que ya tienen imagen principal.',
                'url' => route('admin.properties.index', ['status' => 'draft']),
                'action' => 'Revisar borradores',
                'empty' => 'Aún no hay borradores listos para publicar.',
                'tone' => 'success',
            ],
        ])->filter(fn (array $item) => $permissions[$item['permission']] ?? false)->values()->all();

        $systemChecks = collect([
            [
                'permissions' => ['properties', 'reports'],
                'title' => 'Destacadas activas',
                'help' => 'Propiedades marcadas para portada y listados',
                'badge_class' => 'bg-success-subtle text-success',
                'value' => $stats['properties_featured'],
            ],
            [
                'permissions' => ['zonas', 'reports'],
                'title' => 'Zonas sin portada',
                'help' => 'Conviene revisarlas para cuidar la navegación pública',
                'badge_class' => 'bg-warning-subtle text-warning',
                'value' => $stats['zonas_without_image'],
            ],
            [
                'permissions' => ['properties', 'reports'],
                'title' => 'Borradores vivos',
                'help' => 'Propiedades aún no publicadas',
                'badge_class' => 'bg-secondary',
                'value' => $stats['properties_draft'],
            ],
            [
                'permissions' => ['contacts', 'reports'],
                'title' => 'Leads sin siguiente paso',
                'help' => 'Contactos abiertos que necesitan seguimiento',
                'badge_class' => 'bg-danger',
                'value' => $stats['contactos_without_next_action'],
            ],
        ])->filter(fn (array $item) => $hasAnyPermission($item['permissions']))->values()->all();

        return view('admin.dashboard', compact(
            'permissions',
            'stats',
            'heroActions',
            'overviewCards',
            'latestProperties',
            'latestContacts',
            'propertiesWithoutThumbnail',
            'draftProperties',
            'dueContacts',
            'propertiesByType',
            'workspaceSections',
            'priorityQueue',
            'systemChecks'
        ));
    }
}
