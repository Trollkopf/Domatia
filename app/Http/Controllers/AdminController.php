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
            'users_total' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
        ];

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

        $workspaceSections = [
            [
                'title' => 'Propiedades',
                'description' => 'Gestion del catalogo, revision editorial y publicacion.',
                'metric' => $stats['properties_total'] . ' registradas',
                'tone' => 'primary',
                'links' => [
                    ['label' => 'Ver catalogo', 'url' => route('admin.properties.index')],
                    ['label' => 'Crear propiedad', 'url' => route('admin.properties.create')],
                    ['label' => 'Borradores', 'url' => route('admin.properties.index', ['status' => 'draft'])],
                ],
            ],
            [
                'title' => 'Contactos',
                'description' => 'Seguimiento comercial y proxima accion de cada lead.',
                'metric' => $stats['contactos_total'] . ' leads',
                'tone' => 'warning',
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
                'links' => [
                    ['label' => 'Gestionar zonas', 'url' => route('admin.zonas.index')],
                    ['label' => 'Crear zona', 'url' => route('admin.zonas.create')],
                ],
            ],
            [
                'title' => 'Contenido y ajustes',
                'description' => 'Textos globales, portada y configuracion del sitio.',
                'metric' => 'Home y paginas editables',
                'tone' => 'dark',
                'links' => [
                    ['label' => 'Abrir ajustes', 'url' => route('admin.settings')],
                    ['label' => 'Ver web publica', 'url' => url('/')],
                ],
            ],
            [
                'title' => 'Equipo',
                'description' => 'Usuarios internos con acceso al backoffice.',
                'metric' => $stats['users_total'] . ' usuarios',
                'tone' => 'info',
                'links' => [
                    ['label' => 'Gestionar usuarios', 'url' => route('admin.users.index')],
                ],
            ],
        ];

        $priorityQueue = [
            [
                'title' => 'Leads sin siguiente accion',
                'count' => $stats['contactos_without_next_action'],
                'help' => 'Contactos abiertos que aun no tienen una fecha de seguimiento.',
                'url' => route('admin.contactos.index'),
                'action' => 'Revisar contactos',
                'empty' => 'Todos los leads abiertos tienen siguiente paso definido.',
                'tone' => 'warning',
            ],
            [
                'title' => 'Seguimientos vencidos',
                'count' => $stats['contactos_due'],
                'help' => 'Leads que ya deberian haberse trabajado o tocan hoy.',
                'url' => route('admin.contactos.index', ['follow_up' => 'due']),
                'action' => 'Atender ahora',
                'empty' => 'No hay seguimientos vencidos en este momento.',
                'tone' => 'danger',
            ],
            [
                'title' => 'Propiedades sin imagen principal',
                'count' => $stats['properties_without_thumbnail'],
                'help' => 'Fichas que necesitan una portada para verse bien en listados.',
                'url' => route('admin.properties.index', ['missing_thumbnail' => 1]),
                'action' => 'Completar fichas',
                'empty' => 'Todas las propiedades tienen imagen principal.',
                'tone' => 'primary',
            ],
            [
                'title' => 'Borradores listos para publicar',
                'count' => $stats['drafts_ready_to_publish'],
                'help' => 'Propiedades en borrador que ya tienen imagen principal.',
                'url' => route('admin.properties.index', ['status' => 'draft']),
                'action' => 'Revisar borradores',
                'empty' => 'Aun no hay borradores listos para publicar.',
                'tone' => 'success',
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'latestProperties',
            'latestContacts',
            'propertiesWithoutThumbnail',
            'draftProperties',
            'dueContacts',
            'propertiesByType',
            'workspaceSections',
            'priorityQueue'
        ));
    }
}
