@extends('layouts.admin')

@section('title', 'Informes')

@section('styles')
    <style>
        .reports-shell {
            display: grid;
            gap: 1.5rem;
        }

        .report-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
        }

        .metric-tile {
            border-radius: 18px;
            padding: 1.25rem;
            background: #fff;
            height: 100%;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
        }

        .metric-tile .value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .mini-row + .mini-row {
            border-top: 1px solid #e5e7eb;
        }

        .bar-track {
            height: 10px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #d4a52d 0%, #f59e0b 100%);
        }
    </style>
@endsection

@section('content')
    @php
        $maxLeadStatus = max($leadStatusBreakdown->max('total') ?? 0, 1);
        $maxInventoryType = max($inventoryByType->max('total') ?? 0, 1);
        $maxMonthlyLeads = max($monthlyLeads->max('total') ?? 0, 1);
    @endphp

    <div class="reports-shell">
        <section class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="mb-1">Informes</h1>
                <p class="text-muted mb-0">
                    Lectura rapida del negocio, la calidad del catalogo y el estado comercial.
                    Analizando del {{ \Carbon\Carbon::parse($filters['from'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($filters['to'])->format('d/m/Y') }}.
                </p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.properties.index', array_filter(['zona_id' => $filters['zona_id'], 'tipo' => $filters['tipo']])) }}" class="btn btn-outline-dark">Ir a propiedades</a>
                <a href="{{ route('admin.contactos.index') }}" class="btn btn-outline-dark">Ir a contactos</a>
            </div>
        </section>

        <section class="card report-card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Zona</label>
                        <select name="zona_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($zonas as $zona)
                                <option value="{{ $zona->id }}" @selected((string) $filters['zona_id'] === (string) $zona->id)>{{ $zona->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select">
                            <option value="">Todos</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo }}" @selected($filters['tipo'] === $tipo)>{{ ucfirst($tipo) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-main w-100">Aplicar filtros</button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                    </div>
                </form>
            </div>
        </section>

        <section>
            <div class="row g-3">
                <div class="col-md-6 col-xl-3">
                    <div class="metric-tile">
                        <p class="text-muted mb-2">Propiedades publicadas</p>
                        <div class="value">{{ $overview['published_properties'] }}</div>
                        <div class="small text-muted mt-2">{{ $overview['draft_properties'] }} en borrador</div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="metric-tile">
                        <p class="text-muted mb-2">Leads totales</p>
                        <div class="value">{{ $overview['total_leads'] }}</div>
                        <div class="small text-muted mt-2">{{ $overview['pending_leads'] }} pendientes de trabajo</div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="metric-tile">
                        <p class="text-muted mb-2">Conversion cerrada</p>
                        <div class="value">{{ $conversionRate }}%</div>
                        <div class="small text-muted mt-2">{{ $overview['closed_leads'] }} contactos cerrados</div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="metric-tile">
                        <p class="text-muted mb-2">Precio medio publicado</p>
                        <div class="value">{{ number_format($overview['avg_price'], 0, ',', '.') }}</div>
                        <div class="small text-muted mt-2">{{ $overview['featured_properties'] }} propiedades destacadas</div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h2 class="h5 mb-1">Embudo comercial</h2>
                            <p class="text-muted small mb-0">Distribucion de leads por estado en el periodo filtrado.</p>
                        </div>
                        <div class="card-body pt-3">
                            @forelse ($leadStatusBreakdown as $item)
                                <div class="mini-row py-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">{{ ucfirst($item->status) }}</span>
                                        <span class="text-muted">{{ $item->total }}</span>
                                    </div>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width: {{ ($item->total / $maxLeadStatus) * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay leads para estos filtros.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h2 class="h5 mb-1">Inventario por estado</h2>
                            <p class="text-muted small mb-0">Cuanto del catalogo filtrado esta publicado y cuanto sigue en preparacion.</p>
                        </div>
                        <div class="card-body pt-3">
                            @forelse ($inventoryByStatus as $item)
                                <div class="mini-row py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $item->status === 'published' ? 'Publicadas' : 'Borrador' }}</div>
                                        <div class="small text-muted">Estado editorial del catalogo</div>
                                    </div>
                                    <span class="badge {{ $item->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ $item->total }}</span>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay inventario para los filtros seleccionados.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h2 class="h5 mb-1">Chequeos de calidad</h2>
                            <p class="text-muted small mb-0">Puntos que suelen bloquear ventas o una buena presentacion.</p>
                        </div>
                        <div class="card-body pt-3">
                            @foreach ($qualityChecks as $check)
                                <div class="mini-row py-3 d-flex justify-content-between align-items-center gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $check['label'] }}</div>
                                        <div class="small text-muted">{{ $check['count'] }} elementos detectados</div>
                                    </div>
                                    <a href="{{ $check['url'] }}" class="btn btn-sm btn-outline-dark">{{ $check['action'] }}</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h2 class="h5 mb-1">Ritmo de entrada de leads</h2>
                            <p class="text-muted small mb-0">Evolucion mensual dentro del rango seleccionado.</p>
                        </div>
                        <div class="card-body pt-3">
                            @forelse ($monthlyLeads as $item)
                                <div class="mini-row py-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $item->period)->translatedFormat('M Y') }}</span>
                                        <span class="text-muted">{{ $item->total }}</span>
                                    </div>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width: {{ ($item->total / $maxMonthlyLeads) * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">Aun no hay historico suficiente para mostrar tendencia.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h2 class="h5 mb-1">Inventario por tipo</h2>
                            <p class="text-muted small mb-0">Composicion actual del catalogo dentro del segmento filtrado.</p>
                        </div>
                        <div class="card-body pt-3">
                            @forelse ($inventoryByType as $item)
                                <div class="mini-row py-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">{{ ucfirst($item->tipo) }}</span>
                                        <span class="text-muted">{{ $item->total }}</span>
                                    </div>
                                    <div class="bar-track">
                                        <div class="bar-fill" style="width: {{ ($item->total / $maxInventoryType) * 100 }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay tipos suficientes para analizar.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="h5 mb-1">Propiedades con mas interes</h2>
                                <p class="text-muted small mb-0">Ranking segun contactos asociados en el periodo elegido.</p>
                            </div>
                            <a href="{{ route('admin.properties.index', array_filter(['zona_id' => $filters['zona_id'], 'tipo' => $filters['tipo']])) }}" class="small">Ver catalogo</a>
                        </div>
                        <div class="card-body pt-3">
                            @forelse ($topPropertiesByLeads as $property)
                                <div class="mini-row py-3 d-flex justify-content-between align-items-center gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $property->title }}</div>
                                        <div class="small text-muted">
                                            {{ $property->zona?->nombre ?? 'Sin zona' }} · {{ $property->status === 'published' ? 'Publicada' : 'Borrador' }}
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="fw-semibold">{{ $property->contactos_count }} leads</div>
                                        <a href="{{ route('admin.properties.edit', $property) }}" class="small">Abrir ficha</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay propiedades con historico de contactos para estos filtros.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card report-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="h5 mb-1">Zonas con mejor traccion</h2>
                                <p class="text-muted small mb-0">Cruce entre inventario publicado y demanda recibida.</p>
                            </div>
                            <a href="{{ route('admin.zonas.index') }}" class="small">Gestionar zonas</a>
                        </div>
                        <div class="card-body pt-3">
                            @forelse ($topZones as $zona)
                                <div class="mini-row py-3 d-flex justify-content-between align-items-center gap-3">
                                    <div>
                                        <div class="fw-semibold">{{ $zona->nombre }}</div>
                                        <div class="small text-muted">
                                            {{ $zona->published_properties_count }} publicadas · {{ $zona->properties_count }} totales
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="fw-semibold">{{ $zona->leads_count }} leads</div>
                                        <a href="{{ route('admin.zonas.index') }}" class="small">Ver zona</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay datos suficientes por zonas para este analisis.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
