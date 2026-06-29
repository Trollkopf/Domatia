@extends('layouts.admin')

@section('title', 'Importacion Kyero')

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

        .kyero-side-column .kyero-stack {
            position: sticky;
            top: 6.75rem;
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

        @media (max-width: 1199.98px) {
            .kyero-page {
                grid-template-columns: 1fr;
            }

            .kyero-side-column .kyero-stack {
                position: static;
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
    <div class="kyero-page">
        <div class="kyero-main-column">
            <div class="card kyero-card">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-dark rounded-pill px-3 py-2 mb-3">Importacion manual</span>
                    <h1 class="h3 mb-2">Lanzar importacion de Kyero</h1>
                    <p class="text-muted mb-4">
                        Sube el XML del feed o pega su contenido para actualizar el catalogo desde el backoffice cuando quieras.
                    </p>

                    @if (session('success'))
                        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if ($activeRun && in_array($activeRun->status, ['queued', 'running']))
                        <div class="alert alert-info rounded-4 mb-4">
                            Hay una importacion en curso. Si el proceso en segundo plano esta disponible, seguira avanzando aunque cambies de pagina.
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
                            <label for="max_images_per_property" class="form-label fw-semibold">Maximo de imagenes por propiedad</label>
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
                            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-dark">Volver al catalogo</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                            <h2 class="h5 mb-0">Ultimas ejecuciones</h2>
                            <span class="small text-muted">Solo las ultimas 10</span>
                        </div>

                        <div class="kyero-run-list">
                            @forelse ($latestRuns as $run)
                                <div class="kyero-run-item">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="fw-semibold">{{ $run->input_name ?: 'Importacion manual' }}</div>
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
                                <p class="text-muted mb-0">Todavia no se ha lanzado ninguna importacion.</p>
                            @endforelse
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
