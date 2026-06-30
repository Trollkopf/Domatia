@extends('layouts.admin')

@section('title', 'Listado de Propiedades')

@section('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Propiedades</h1>
            <p class="text-muted mb-0">Filtra y gestiona el catálogo desde un solo sitio.</p>
        </div>
        <a href="{{ route('admin.properties.create') }}" class="btn btn-main">+ Nueva Propiedad</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert d-none" role="status" aria-live="polite" data-property-feedback></div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.properties.index') }}" class="row g-3" data-property-filters>
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Título, ubicación o referencia">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Zona</label>
                    <select name="zona_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}" @selected((string) request('zona_id') === (string) $zona->id)>{{ $zona->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Destacada</label>
                    <select name="featured" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" @selected(request('featured') === '1')>Sí</option>
                        <option value="0" @selected(request('featured') === '0')>No</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Desde</label>
                    <input type="number" name="price_min" class="form-control" value="{{ request('price_min') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Hasta</label>
                    <input type="number" name="price_max" class="form-control" value="{{ request('price_max') }}">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4 pt-2">
                        <input type="checkbox" class="form-check-input" id="missing_thumbnail" name="missing_thumbnail" value="1" @checked(request()->boolean('missing_thumbnail'))>
                        <label class="form-check-label" for="missing_thumbnail">Solo sin imagen principal</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-main w-100">Aplicar filtros</button>
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    @if (auth()->user()?->canPublishProperties())
        <div class="card shadow-sm border-0 mb-3" data-bulk-actions data-bulk-publish-url="{{ route('admin.properties.bulk-publish') }}">
            <div class="card-body py-3 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <div class="fw-semibold"><span data-selected-count>0</span> borradores seleccionados</div>
                    <div class="small text-muted">Puedes marcar los borradores visibles o publicar todos los que coincidan con los filtros.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-dark" data-publish-selected disabled>Publicar seleccionados</button>
                    <button type="button" class="btn btn-main" data-publish-filtered @disabled($matchingDraftCount === 0)>
                        Publicar borradores filtrados (<span data-filtered-draft-count>{{ $matchingDraftCount }}</span>)
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @include('admin.properties._table', ['properties' => $properties, 'statuses' => $statuses])
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $properties->links('pagination::bootstrap-5') }}
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = @json(csrf_token());
            const filtersForm = document.querySelector('[data-property-filters]');
            const feedback = document.querySelector('[data-property-feedback]');
            const bulkActions = document.querySelector('[data-bulk-actions]');
            const selectPage = document.querySelector('[data-select-page]');
            const selectedCount = document.querySelector('[data-selected-count]');
            const publishSelected = document.querySelector('[data-publish-selected]');
            const publishFiltered = document.querySelector('[data-publish-filtered]');
            const filteredDraftCount = document.querySelector('[data-filtered-draft-count]');
            let remainingDraftCount = @json($matchingDraftCount);
            const statusClasses = {
                draft: 'bg-secondary',
                published: 'bg-success',
                reserved: 'bg-warning text-dark',
                sold: 'bg-dark',
                hidden: 'bg-light text-dark',
            };

            function showFeedback(message, type = 'success') {
                if (!feedback) {
                    return;
                }

                feedback.textContent = message;
                feedback.className = 'alert alert-' + type;
            }

            function selectableCheckboxes() {
                return Array.from(document.querySelectorAll('[data-property-select]:not(:disabled)'));
            }

            function selectedIds() {
                return selectableCheckboxes().filter(checkbox => checkbox.checked).map(checkbox => Number(checkbox.value));
            }

            function syncSelection() {
                const available = selectableCheckboxes();
                const selected = available.filter(checkbox => checkbox.checked);

                if (selectedCount) {
                    selectedCount.textContent = String(selected.length);
                }

                if (publishSelected) {
                    publishSelected.disabled = selected.length === 0;
                }

                if (selectPage) {
                    selectPage.checked = available.length > 0 && selected.length === available.length;
                    selectPage.indeterminate = selected.length > 0 && selected.length < available.length;
                    selectPage.disabled = available.length === 0;
                }
            }

            function currentFilters() {
                const filters = {};

                if (!filtersForm) {
                    return filters;
                }

                new FormData(filtersForm).forEach(function (value, key) {
                    if (value !== '') {
                        filters[key] = value;
                    }
                });

                return filters;
            }

            function updatePropertyRow(payload) {
                const row = document.querySelector('[data-property-row="' + payload.property_id + '"]');

                if (!row) {
                    return;
                }

                const statusBadge = row.querySelector('[data-property-status-badge]');
                const statusSelect = row.querySelector('[data-status-select]');
                const propertyCheckbox = row.querySelector('[data-property-select]');

                if (statusBadge && payload.status) {
                    statusBadge.className = 'badge ' + (statusClasses[payload.status] || 'bg-secondary');
                    statusBadge.textContent = payload.status_label;
                }

                if (statusSelect && payload.status) {
                    statusSelect.value = payload.status;
                }

                if (propertyCheckbox && payload.status) {
                    propertyCheckbox.checked = false;
                    propertyCheckbox.disabled = payload.status !== 'draft';
                }

                if (typeof payload.is_featured === 'boolean') {
                    const featuredBadge = row.querySelector('[data-property-featured-badge]');
                    const featuredButton = row.querySelector('[data-featured-button]');

                    if (featuredBadge) {
                        featuredBadge.className = 'badge ' + (payload.is_featured ? 'bg-success' : 'bg-secondary');
                        featuredBadge.textContent = payload.is_featured ? 'Sí' : 'No';
                    }

                    if (featuredButton) {
                        featuredButton.textContent = payload.is_featured ? 'Quitar destacada' : 'Destacar';
                    }
                }

                const filters = currentFilters();
                const outsideStatusFilter = filters.status && payload.status && filters.status !== payload.status;
                const outsideFeaturedFilter = filters.featured !== undefined
                    && typeof payload.is_featured === 'boolean'
                    && filters.featured !== (payload.is_featured ? '1' : '0');

                if (outsideStatusFilter || outsideFeaturedFilter) {
                    row.remove();
                }

                syncSelection();
            }

            async function submitQuickUpdate(form) {
                if (form.dataset.busy === 'true') {
                    return;
                }

                form.dataset.busy = 'true';
                form.querySelectorAll('button, select').forEach(control => control.disabled = true);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });
                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'No se pudo actualizar la propiedad.');
                    }

                    updatePropertyRow(payload);
                    showFeedback(payload.message);
                } catch (error) {
                    showFeedback(error.message, 'danger');
                } finally {
                    form.dataset.busy = 'false';
                    form.querySelectorAll('button, select').forEach(control => control.disabled = false);
                }
            }

            document.querySelectorAll('[data-property-quick-update]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    submitQuickUpdate(form);
                });

                form.querySelector('[data-status-select]')?.addEventListener('change', function () {
                    submitQuickUpdate(form);
                });
            });

            document.querySelectorAll('[data-property-select]').forEach(checkbox => checkbox.addEventListener('change', syncSelection));

            selectPage?.addEventListener('change', function () {
                selectableCheckboxes().forEach(checkbox => checkbox.checked = selectPage.checked);
                syncSelection();
            });

            async function bulkPublish(scope) {
                const ids = selectedIds();

                if (scope === 'selected' && ids.length === 0) {
                    return;
                }

                const amount = scope === 'selected' ? ids.length : remainingDraftCount;
                const question = scope === 'selected'
                    ? `¿Publicar los ${amount} borradores seleccionados?`
                    : `¿Publicar los ${amount} borradores que coinciden con los filtros actuales?`;

                if (!window.confirm(question)) {
                    return;
                }

                publishSelected && (publishSelected.disabled = true);
                publishFiltered && (publishFiltered.disabled = true);

                try {
                    const response = await fetch(bulkActions.dataset.bulkPublishUrl, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            scope: scope,
                            property_ids: scope === 'selected' ? ids : [],
                            filters: currentFilters(),
                        }),
                        credentials: 'same-origin',
                    });
                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'No se pudieron publicar las propiedades.');
                    }

                    payload.property_ids.forEach(function (propertyId) {
                        updatePropertyRow({
                            property_id: propertyId,
                            status: 'published',
                            status_label: 'Publicada',
                        });
                    });
                    showFeedback(payload.message);

                    remainingDraftCount = Math.max(0, remainingDraftCount - payload.updated_count);

                    if (filteredDraftCount) {
                        filteredDraftCount.textContent = String(remainingDraftCount);
                    }

                    if (scope === 'filtered') {
                        window.setTimeout(() => window.location.reload(), 700);
                    } else if (publishFiltered) {
                        publishFiltered.disabled = remainingDraftCount === 0;
                    }
                } catch (error) {
                    showFeedback(error.message, 'danger');
                    syncSelection();
                    publishFiltered && (publishFiltered.disabled = remainingDraftCount === 0);
                }
            }

            publishSelected?.addEventListener('click', () => bulkPublish('selected'));
            publishFiltered?.addEventListener('click', () => bulkPublish('filtered'));
            syncSelection();
        });
    </script>
@endpush
