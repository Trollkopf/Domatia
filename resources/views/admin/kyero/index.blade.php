@extends('layouts.admin')

@section('title', 'Importación Kyero')

@section('styles')
    <style>
        .kyero-page {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(340px, 0.8fr);
            gap: 1.5rem;
            align-items: start;
        }

        .kyero-main-column,
        .kyero-side-column {
            min-width: 0;
        }

        .kyero-stack {
            display: grid;
            gap: 1.5rem;
        }

        .kyero-card {
            border: 0;
            border-radius: 1.5rem;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .kyero-run-list {
            display: grid;
            gap: 1rem;
        }

        .kyero-run-item {
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            padding: 1rem;
            background: #f8fafc;
        }

        .kyero-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem 1rem;
        }

        .kyero-progress-meta {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .kyero-feed-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .kyero-feed-item {
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            padding: 1rem;
            background: #f8fafc;
        }

        @media (max-width: 1199.98px) {
            .kyero-page {
                grid-template-columns: 1fr;
            }

            .kyero-feed-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 575.98px) {
            .kyero-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="mb-1">Importaciones Kyero</h1>
            <p class="text-muted mb-0">Guarda feeds automáticos, ejecútalos cuando quieras y revisa su historial.</p>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-dark">Volver al catálogo</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4">{{ $errors->first() }}</div>
    @endif

    @php
        $kyeroInitialTab = $activeRun
            ? 'runs'
            : (($errors->has('xml_file') || $errors->has('xml_content')) ? 'manual' : 'feeds');
    @endphp

    <ul class="nav admin-form-tabs mb-4" id="kyeroSectionTabs" role="tablist" aria-label="Secciones de importación Kyero">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $kyeroInitialTab === 'feeds' ? 'active' : '' }}" id="kyero-feeds-tab" data-bs-toggle="tab" data-bs-target="#kyero-feeds-pane" type="button" role="tab" aria-controls="kyero-feeds-pane" aria-selected="{{ $kyeroInitialTab === 'feeds' ? 'true' : 'false' }}">Fuentes automáticas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $kyeroInitialTab === 'manual' ? 'active' : '' }}" id="kyero-manual-tab" data-bs-toggle="tab" data-bs-target="#kyero-manual-pane" type="button" role="tab" aria-controls="kyero-manual-pane" aria-selected="{{ $kyeroInitialTab === 'manual' ? 'true' : 'false' }}">Importación manual</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $kyeroInitialTab === 'runs' ? 'active' : '' }}" id="kyero-runs-tab" data-bs-toggle="tab" data-bs-target="#kyero-runs-pane" type="button" role="tab" aria-controls="kyero-runs-pane" aria-selected="{{ $kyeroInitialTab === 'runs' ? 'true' : 'false' }}">Ejecuciones</button>
        </li>
    </ul>

    <div class="tab-content" id="kyeroSectionTabContent">
    <div class="tab-pane fade {{ $kyeroInitialTab === 'feeds' ? 'show active' : '' }}" id="kyero-feeds-pane" role="tabpanel" aria-labelledby="kyero-feeds-tab" tabindex="0">

    <div class="card kyero-card mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                <div>
                    <span class="badge rounded-pill px-3 py-2 mb-2" style="background:#d4a52d;color:#111827;">Automatización</span>
                    <h2 class="h4 mb-1">Fuentes Kyero automáticas</h2>
                    <p class="text-muted mb-0">Las fuentes activas se descargan cada noche a las {{ config('kyero.schedule_time') }} ({{ config('kyero.schedule_timezone') }}).</p>
                </div>
                <div class="small text-muted align-self-lg-end">El servidor debe ejecutar <code>php artisan schedule:run</code> cada minuto.</div>
            </div>

            <form action="{{ route('admin.kyero.feeds.store') }}" method="POST" class="row g-3 align-items-end mb-4">
                @csrf
                <div class="col-lg-3">
                    <label for="feed_name" class="form-label">Nombre</label>
                    <input type="text" id="feed_name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Feed principal" required>
                </div>
                <div class="col-lg-5">
                    <label for="feed_url" class="form-label">URL del XML</label>
                    <input type="url" id="feed_url" name="url" class="form-control" value="{{ old('url') }}" placeholder="https://proveedor.example/feed.xml" required>
                </div>
                <div class="col-sm-5 col-lg-2">
                    <label for="feed_max_images" class="form-label">Máx. imágenes</label>
                    <input type="number" id="feed_max_images" name="max_images_per_property" class="form-control" min="1" max="30" value="{{ old('max_images_per_property', 12) }}" required>
                </div>
                <div class="col-sm-3 col-lg-1">
                    <div class="form-check mb-2">
                        <input type="checkbox" id="feed_active" name="is_active" class="form-check-input" value="1" @checked(old('is_active', true))>
                        <label for="feed_active" class="form-check-label">Activa</label>
                    </div>
                </div>
                <div class="col-sm-4 col-lg-1 d-grid">
                    <button type="submit" class="btn btn-main">Añadir</button>
                </div>
            </form>

            <div class="kyero-feed-grid">
                @forelse ($feeds as $feed)
                    <div class="kyero-feed-item">
                        <form action="{{ route('admin.kyero.feeds.update', $feed) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" value="{{ $feed->name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Máx. imágenes</label>
                                <input type="number" name="max_images_per_property" class="form-control" min="1" max="30" value="{{ $feed->max_images_per_property }}" required>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input type="checkbox" id="feed-active-{{ $feed->id }}" name="is_active" class="form-check-input" value="1" @checked($feed->is_active)>
                                    <label for="feed-active-{{ $feed->id }}" class="form-check-label">Activa</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">URL</label>
                                <input type="url" name="url" class="form-control" value="{{ $feed->url }}" required>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                <div class="small text-muted">
                                    Estado:
                                    <span class="badge {{ $feed->last_status === 'completed' ? 'bg-success' : ($feed->last_status === 'failed' ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ $feed->last_status ?: 'Sin ejecutar' }}
                                    </span>
                                    @if ($feed->last_run_at)
                                        · {{ $feed->last_run_at->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-dark">Guardar cambios</button>
                            </div>
                        </form>

                        @if ($feed->last_error)
                            <div class="small text-danger mt-2">{{ $feed->last_error }}</div>
                        @endif

                        <div class="d-flex gap-2 mt-3">
                            <form action="{{ route('admin.kyero.feeds.run', $feed) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-main" onclick="return confirm('¿Ejecutar esta fuente ahora?')">Ejecutar ahora</button>
                            </form>
                            <form action="{{ route('admin.kyero.feeds.destroy', $feed) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta fuente automática?')">Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">Todavía no hay fuentes guardadas. Añade la URL que te proporciona Kyero para automatizarla.</div>
                @endforelse
            </div>
        </div>
    </div>

    </div>

    <div class="tab-pane fade {{ $kyeroInitialTab === 'manual' ? 'show active' : '' }}" id="kyero-manual-pane" role="tabpanel" aria-labelledby="kyero-manual-tab" tabindex="0">
            <div class="card kyero-card">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-dark rounded-pill px-3 py-2 mb-3">Importación manual</span>
                    <h2 class="h3 mb-2">Lanzar importacion de Kyero</h2>
                    <p class="text-muted mb-4">
                        Sube el XML del feed o pega su contenido para actualizar el catálogo desde el backoffice cuando quieras.
                    </p>

                    @if ($activeRun && in_array($activeRun->status, ['queued', 'running']))
                        <div class="alert alert-info rounded-4 mb-4">
                            Hay una importación en curso. Si el proceso en segundo plano está disponible, seguirá avanzando aunque cambies de página.
                        </div>
                    @endif

                    <form action="{{ route('admin.kyero.store') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-4">
                        @csrf

                        <div>
                            <label for="xml_file" class="form-label fw-semibold">Archivo XML</label>
                            <input type="file" class="form-control" id="xml_file" name="xml_file" accept=".xml,text/xml">
                            <div class="form-text">Ideal para lanzar importaciones puntuales desde un feed exportado.</div>
                        </div>

                        <div>
                            <label for="xml_content" class="form-label fw-semibold">O pegar XML</label>
                            <textarea
                                class="form-control"
                                id="xml_content"
                                name="xml_content"
                                rows="14"
                                placeholder="<properties><property>...</property></properties>"
                            >{{ old('xml_content') }}</textarea>
                            <div class="form-text">Si rellenas ambos campos, se usara el archivo subido.</div>
                        </div>

                        <div>
                            <label for="max_images_per_property" class="form-label fw-semibold">Máximo de imágenes por propiedad</label>
                            <input
                                type="number"
                                class="form-control"
                                id="max_images_per_property"
                                name="max_images_per_property"
                                min="1"
                                max="30"
                                value="{{ old('max_images_per_property', 12) }}"
                            >
                            <div class="form-text">Limitar fotos por ficha ayuda a evitar bloqueos en importaciones grandes.</div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-main">Preparar importacion</button>
                            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-dark">Volver al catálogo</a>
                        </div>
                    </form>
                </div>
            </div>
    </div>

    <div class="tab-pane fade {{ $kyeroInitialTab === 'runs' ? 'show active' : '' }}" id="kyero-runs-pane" role="tabpanel" aria-labelledby="kyero-runs-tab" tabindex="0">
        <div class="kyero-side-column">
            <div class="kyero-stack">
                <div class="card kyero-card">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Estado de la importacion</h2>

                        @if ($activeRun)
                            <div id="kyero-run-panel" data-run-id="{{ $activeRun->id }}" data-run-status="{{ $activeRun->status }}">
                                <div class="kyero-progress-meta small text-muted mb-2">
                                    <span>Progreso</span>
                                    <span id="kyero-progress-label">{{ $activeRun->properties_seen }}/{{ $activeRun->total_properties }}</span>
                                </div>
                                <div class="progress rounded-pill" style="height: 12px;">
                                    <div
                                        id="kyero-progress-bar"
                                        class="progress-bar {{ $activeRun->status === 'completed' ? 'bg-success' : ($activeRun->status === 'failed' ? 'bg-danger' : 'bg-dark') }}"
                                        role="progressbar"
                                        style="width: {{ $activeRun->total_properties > 0 ? intval(($activeRun->properties_seen / $activeRun->total_properties) * 100) : 0 }}%;"
                                    ></div>
                                </div>

                                <div class="kyero-stats-grid mt-3 small">
                                    <div>Leidas: <strong id="kyero-seen">{{ $activeRun->properties_seen }}</strong></div>
                                    <div>Total: <strong id="kyero-total">{{ $activeRun->total_properties }}</strong></div>
                                    <div>Creadas: <strong id="kyero-created">{{ $activeRun->properties_created }}</strong></div>
                                    <div>Actualizadas: <strong id="kyero-updated">{{ $activeRun->properties_updated }}</strong></div>
                                    <div>Omitidas: <strong id="kyero-skipped">{{ $activeRun->properties_skipped }}</strong></div>
                                    <div>Imagenes: <strong id="kyero-images">{{ $activeRun->images_downloaded }}</strong></div>
                                </div>

                                <div id="kyero-status-note" class="small text-muted mt-3">{{ $activeRun->notes }}</div>

                                @if (in_array($activeRun->status, ['queued', 'running']))
                                    <div class="d-grid mt-3">
                                        <button type="button" class="btn btn-outline-dark btn-sm" id="kyero-manual-process">
                                            Procesar una tanda manualmente
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-muted mb-0">Cuando prepares una importacion, aqui veras el progreso y el estado del proceso.</p>
                        @endif
                    </div>
                </div>

                <div class="card kyero-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <h2 class="h5 mb-0">Últimas ejecuciones</h2>
                            <span class="small text-muted">Solo las ultimas 10</span>
                        </div>

                        <div class="kyero-run-list">
                            @forelse ($latestRuns as $run)
                                <div class="kyero-run-item">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $run->input_name ?: 'Importación manual' }}</div>
                                            <div class="small text-muted">
                                                {{ $run->started_at?->format('d/m/Y H:i') ?? '-' }}
                                                @if ($run->user)
                                                    &middot; {{ $run->user->name }}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="badge {{ $run->status === 'completed' ? 'bg-success' : ($run->status === 'failed' ? 'bg-danger' : 'bg-secondary') }}">
                                            {{ $run->status === 'completed' ? 'Completada' : ($run->status === 'failed' ? 'Fallida' : 'En curso') }}
                                        </span>
                                    </div>

                                    <div class="kyero-stats-grid mt-3 small">
                                        <div>Leidas: <strong>{{ $run->properties_seen }}</strong></div>
                                        <div>Creadas: <strong>{{ $run->properties_created }}</strong></div>
                                        <div>Actualizadas: <strong>{{ $run->properties_updated }}</strong></div>
                                        <div>Omitidas: <strong>{{ $run->properties_skipped }}</strong></div>
                                    </div>

                                    @if ($run->notes)
                                        <div class="small text-muted mt-2">{{ $run->notes }}</div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-0">Todavía no se ha lanzado ninguna importación.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    @if ($activeRun)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const panel = document.getElementById('kyero-run-panel');

                if (!panel) {
                    return;
                }

                const runId = panel.dataset.runId;
                const manualButton = document.getElementById('kyero-manual-process');
                let busy = false;

                const updateUi = function (run) {
                    const label = document.getElementById('kyero-progress-label');
                    const bar = document.getElementById('kyero-progress-bar');
                    const note = document.getElementById('kyero-status-note');

                    if (label) {
                        label.textContent = `${run.properties_seen}/${run.total_properties}`;
                    }

                    if (bar) {
                        bar.style.width = `${run.progress}%`;
                        bar.className = `progress-bar ${run.status === 'completed' ? 'bg-success' : (run.status === 'failed' ? 'bg-danger' : 'bg-dark')}`;
                    }

                    if (note) {
                        note.textContent = run.notes || '';
                    }

                    document.getElementById('kyero-seen').textContent = run.properties_seen;
                    document.getElementById('kyero-total').textContent = run.total_properties;
                    document.getElementById('kyero-created').textContent = run.properties_created;
                    document.getElementById('kyero-updated').textContent = run.properties_updated;
                    document.getElementById('kyero-skipped').textContent = run.properties_skipped;
                    document.getElementById('kyero-images').textContent = run.images_downloaded;

                    if (manualButton && (run.status === 'completed' || run.status === 'failed')) {
                        manualButton.disabled = true;
                    }
                };

                const fetchStatus = async function () {
                    const response = await fetch(`{{ url('/admin/kyero') }}/${runId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    return response.json();
                };

                const processChunk = async function () {
                    const response = await fetch(`{{ url('/admin/kyero') }}/${runId}/process`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    return response.json();
                };

                const tick = async function () {
                    if (busy) {
                        return;
                    }

                    busy = true;

                    try {
                        const payload = await fetchStatus();

                        if (payload.ok) {
                            updateUi(payload.run);
                        }
                    } catch (error) {
                    } finally {
                        busy = false;
                        window.setTimeout(tick, 2000);
                    }
                };

                if (manualButton) {
                    manualButton.addEventListener('click', async function () {
                        if (busy) {
                            return;
                        }

                        busy = true;
                        manualButton.disabled = true;

                        try {
                            const payload = await processChunk();

                            if (payload.ok) {
                                updateUi(payload.run);
                            }
                        } catch (error) {
                        } finally {
                            busy = false;

                            if (!manualButton.disabled) {
                                manualButton.disabled = false;
                            }
                        }
                    });
                }

                tick();
            });
        </script>
    @endif
@endpush
