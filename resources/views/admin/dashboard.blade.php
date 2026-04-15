@extends('layouts.admin')

@section('title', 'Dashboard')

@section('styles')
    <style>
        .dashboard-shell {
            display: grid;
            gap: 1.5rem;
        }

        .hero-panel {
            background: linear-gradient(135deg, #111827 0%, #1f2937 55%, #374151 100%);
            color: #fff;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        }

        .metric-card,
        .section-card,
        .panel-card,
        .queue-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
        }

        .metric-card .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .section-card {
            height: 100%;
        }

        .section-card .tone-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            display: inline-block;
        }

        .queue-count {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .mini-list-item + .mini-list-item {
            border-top: 1px solid #e5e7eb;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-shell">
        <section class="hero-panel">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2 mb-3">Panel operativo</span>
                    <h1 class="display-6 mb-2">Backoffice listo para trabajar por prioridades</h1>
                    <p class="mb-0 text-white-50">
                        Controla el catalogo, revisa leads pendientes y entra a cada seccion con filtros ya preparados.
                    </p>
                </div>

                <div class="col-lg-4">
                    @if (count($heroActions) > 0)
                        <div class="d-grid gap-2">
                            @foreach ($heroActions as $action)
                                <a href="{{ $action['url'] }}" class="btn {{ $action['style'] }}">{{ $action['label'] }}</a>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-4 border border-light border-opacity-25 p-3 small text-white-50">
                            Tu grupo tiene acceso al panel, pero no hay acciones directas asignadas en este momento.
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section>
            <div class="row g-3">
                @forelse ($overviewCards as $card)
                    <div class="col-md-6 col-xl-3">
                        <div class="card metric-card bg-white h-100">
                            <div class="card-body">
                                <p class="text-muted mb-2">{{ $card['label'] }}</p>
                                <div class="metric-value">{{ $card['value'] }}</div>
                                <p class="small text-muted mb-0">{{ $card['help'] }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card metric-card bg-white">
                            <div class="card-body">
                                <p class="mb-0 text-muted">Todavia no hay metricas disponibles para este perfil de acceso.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        @if (count($priorityQueue) > 0)
            <section>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h4 mb-1">Prioridades del dia</h2>
                        <p class="text-muted mb-0">Tareas que conviene resolver primero para que el backoffice fluya.</p>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach ($priorityQueue as $item)
                        <div class="col-md-6 col-xl-3">
                            <div class="card queue-card h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="queue-count bg-{{ $item['tone'] }} bg-opacity-10 text-{{ $item['tone'] }}">
                                            {{ $item['count'] }}
                                        </div>
                                    </div>

                                    <h3 class="h5">{{ $item['title'] }}</h3>
                                    <p class="text-muted small flex-grow-1 mb-3">{{ $item['help'] }}</p>

                                    @if ($item['count'] > 0)
                                        <a href="{{ $item['url'] }}" class="btn btn-outline-dark btn-sm">{{ $item['action'] }}</a>
                                    @else
                                        <div class="small text-success">{{ $item['empty'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h4 mb-1">Secciones de trabajo</h2>
                    <p class="text-muted mb-0">Entradas rapidas para gestionar cada area con contexto.</p>
                </div>
            </div>

            @if (count($workspaceSections) > 0)
                <div class="row g-3">
                    @foreach ($workspaceSections as $section)
                        <div class="col-md-6 col-xl-4">
                            <div class="card section-card">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <span class="tone-dot bg-{{ $section['tone'] }}"></span>
                                        <span class="small text-uppercase text-muted">{{ $section['metric'] }}</span>
                                    </div>

                                    <h3 class="h5">{{ $section['title'] }}</h3>
                                    <p class="text-muted small flex-grow-1">{{ $section['description'] }}</p>

                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($section['links'] as $link)
                                            <a href="{{ $link['url'] }}" class="btn btn-sm btn-outline-dark">{{ $link['label'] }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card section-card">
                    <div class="card-body">
                        <p class="mb-0 text-muted">No hay secciones operativas visibles para este grupo todavia.</p>
                    </div>
                </div>
            @endif
        </section>

        @if (count($systemChecks) > 0 || $permissions['properties'] || $permissions['contacts'])
            <section>
                <div class="row g-4">
                    @if (count($systemChecks) > 0)
                        <div class="col-xl-4">
                            <div class="card panel-card h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4">
                                    <h2 class="h5 mb-1">Salud del sistema</h2>
                                    <p class="text-muted small mb-0">Una vista rapida para detectar bloqueo operativo.</p>
                                </div>
                                <div class="card-body pt-3">
                                    @foreach ($systemChecks as $item)
                                        <div class="mini-list-item py-3 d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold">{{ $item['title'] }}</div>
                                                <div class="small text-muted">{{ $item['help'] }}</div>
                                            </div>
                                            <span class="badge {{ $item['badge_class'] }}">{{ $item['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($permissions['properties'])
                        <div class="col-xl-4">
                            <div class="card panel-card h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="h5 mb-1">Ultimas propiedades</h2>
                                        <p class="text-muted small mb-0">Las fichas en las que se ha estado trabajando mas recientemente.</p>
                                    </div>
                                    <a href="{{ route('admin.properties.index') }}" class="small">Ver catalogo</a>
                                </div>
                                <div class="card-body pt-3">
                                    @forelse ($latestProperties as $property)
                                        <div class="mini-list-item py-3 d-flex justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $property->title }}</div>
                                                <div class="small text-muted">
                                                    {{ $property->ref ?? 'Sin referencia' }} - {{ $property->location ?? 'Ubicacion no indicada' }}
                                                </div>
                                                <div class="small mt-1">
                                                    <span class="badge {{ $property->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $property->status === 'published' ? 'Publicada' : 'Borrador' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <div class="small text-muted mb-1">{{ $property->created_at->format('d/m/Y') }}</div>
                                                <a href="{{ route('admin.properties.edit', $property) }}" class="small">Editar</a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">Todavia no hay propiedades creadas.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($permissions['contacts'])
                        <div class="col-xl-4">
                            <div class="card panel-card h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="h5 mb-1">Actividad comercial</h2>
                                        <p class="text-muted small mb-0">Ultimos leads y seguimientos que requieren atencion.</p>
                                    </div>
                                    <a href="{{ route('admin.contactos.index') }}" class="small">Abrir bandeja</a>
                                </div>
                                <div class="card-body pt-3">
                                    @forelse ($latestContacts as $contacto)
                                        <div class="mini-list-item py-3 d-flex justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $contacto->nombre }}</div>
                                                <div class="small text-muted">{{ $contacto->email }}</div>
                                                <div class="small text-muted">
                                                    {{ $contacto->property?->title ?? 'Sin propiedad asociada' }}
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <span class="badge bg-{{ $contacto->status === 'pendiente' ? 'warning text-dark' : ($contacto->status === 'contactado' ? 'info text-dark' : 'success') }}">
                                                    {{ ucfirst($contacto->status) }}
                                                </span>
                                                <div class="mt-1">
                                                    <a href="{{ route('admin.contactos.show', $contacto) }}" class="small">Ver</a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">Todavia no hay contactos registrados.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($permissions['properties'] || $permissions['contacts'])
            <section>
                <div class="row g-4">
                    @if ($permissions['properties'])
                        <div class="col-lg-6">
                            <div class="card panel-card h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="h5 mb-1">Pendientes de imagen principal</h2>
                                        <p class="text-muted small mb-0">Fichas que necesitan mejorar presentacion publica.</p>
                                    </div>
                                    <a href="{{ route('admin.properties.index', ['missing_thumbnail' => 1]) }}" class="small">Ver todas</a>
                                </div>
                                <div class="card-body pt-3">
                                    @forelse ($propertiesWithoutThumbnail as $property)
                                        <div class="mini-list-item py-3 d-flex justify-content-between align-items-center gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $property->title }}</div>
                                                <div class="small text-muted">{{ $property->ref ?? 'Sin referencia' }}</div>
                                            </div>

                                            <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-sm btn-outline-primary">Completar</a>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">Todo el catalogo tiene imagen principal asignada.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($permissions['contacts'])
                        <div class="col-lg-6">
                            <div class="card panel-card h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="h5 mb-1">Seguimientos vencidos</h2>
                                        <p class="text-muted small mb-0">Leads que ya piden respuesta o llamada.</p>
                                    </div>
                                    <a href="{{ route('admin.contactos.index', ['follow_up' => 'due']) }}" class="small">Ver todos</a>
                                </div>
                                <div class="card-body pt-3">
                                    @forelse ($dueContacts as $contacto)
                                        <div class="mini-list-item py-3 d-flex justify-content-between align-items-center gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $contacto->nombre }}</div>
                                                <div class="small text-muted">
                                                    {{ $contacto->next_action_at?->format('d/m/Y') ?? 'Sin fecha' }} - {{ $contacto->property?->title ?? 'Sin propiedad asociada' }}
                                                </div>
                                            </div>

                                            <a href="{{ route('admin.contactos.show', $contacto) }}" class="btn btn-sm btn-outline-danger">Gestionar</a>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No hay seguimientos vencidos ahora mismo.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($permissions['properties'] || $permissions['reports'])
            <section>
                <div class="row g-4">
                    @if ($permissions['properties'])
                        <div class="col-lg-6">
                            <div class="card panel-card h-100">
                                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h2 class="h5 mb-1">Pendientes de publicacion</h2>
                                        <p class="text-muted small mb-0">Borradores que siguen en cola editorial.</p>
                                    </div>
                                    <a href="{{ route('admin.properties.index', ['status' => 'draft']) }}" class="small">Ver borradores</a>
                                </div>
                                <div class="card-body pt-3">
                                    @forelse ($draftProperties as $property)
                                        <div class="mini-list-item py-3 d-flex justify-content-between align-items-center gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $property->title }}</div>
                                                <div class="small text-muted">{{ $property->ref ?? 'Sin referencia' }}</div>
                                            </div>

                                            <a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-sm btn-outline-primary">Revisar</a>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No hay propiedades en borrador ahora mismo.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-lg-6">
                        <div class="card panel-card h-100">
                            <div class="card-header bg-white border-0 pt-4 px-4">
                                <h2 class="h5 mb-1">Distribucion por tipo</h2>
                                <p class="text-muted small mb-0">Composicion actual del catalogo.</p>
                            </div>
                            <div class="card-body pt-3">
                                <div class="row g-3">
                                    @forelse ($propertiesByType as $item)
                                        <div class="col-md-6">
                                            <div class="border rounded-4 bg-light p-3 h-100">
                                                <div class="small text-muted text-uppercase">{{ $item->tipo ?: 'Sin tipo' }}</div>
                                                <div class="fs-4 fw-semibold">{{ $item->total }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">Aun no hay datos para mostrar.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
