@csrf

@php
    $canPublishProperties = auth()->user()?->canPublishProperties();
    $statusValue = old('status', $property->status ?? 'draft');
    $statusLabels = [
        'draft' => 'Borrador',
        'published' => 'Publicada',
        'reserved' => 'Reservada',
        'sold' => 'Vendida',
        'hidden' => 'Oculta',
    ];
    $propertyTranslationLocales = collect(config('app.supported_locales'))->except(config('app.locale'))->all();
    $featureTextareaValue = old('features_text', isset($property) ? implode("\n", $property->featuresList()) : '');
@endphp

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-1">Revisa los campos indicados antes de guardar.</div>
        <div class="small">La pestaña con el primer error se ha abierto automáticamente.</div>
    </div>
@endif

<ul class="nav admin-form-tabs mb-4" id="propertyFormTabs" role="tablist" aria-label="Secciones de la propiedad">
    <li class="nav-item" role="presentation">
        <button class="nav-link active text-nowrap" id="main-tab" data-bs-toggle="tab" data-bs-target="#property-main" type="button" role="tab" aria-controls="property-main" aria-selected="true">Principal</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-nowrap" id="location-tab" data-bs-toggle="tab" data-bs-target="#property-location" type="button" role="tab" aria-controls="property-location" aria-selected="false">Ubicación</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-nowrap" id="features-tab" data-bs-toggle="tab" data-bs-target="#property-features" type="button" role="tab" aria-controls="property-features" aria-selected="false">Características</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-nowrap" id="media-tab" data-bs-toggle="tab" data-bs-target="#property-media" type="button" role="tab" aria-controls="property-media" aria-selected="false">Multimedia</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-nowrap" id="languages-tab" data-bs-toggle="tab" data-bs-target="#property-languages" type="button" role="tab" aria-controls="property-languages" aria-selected="false">Idiomas</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link text-nowrap" id="images-tab" data-bs-toggle="tab" data-bs-target="#property-images" type="button" role="tab" aria-controls="property-images" aria-selected="false">Imágenes</button>
    </li>
</ul>

<div class="tab-content" id="propertyFormTabContent">
<div class="tab-pane fade show active" id="property-main" role="tabpanel" aria-labelledby="main-tab" tabindex="0">
<div class="d-grid gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Contenido principal</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Título base (ES)</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $property->title ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tipo</label>
                    <input type="text" name="tipo" class="form-control" value="{{ old('tipo', $property->tipo ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Ubicación base (ES)</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $property->location ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $property->price ?? '') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Moneda</label>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency', $property->currency ?? 'EUR') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Frecuencia de precio</label>
                    <input type="text" name="price_freq" class="form-control" value="{{ old('price_freq', $property->price_freq ?? 'sale') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    @if ($canPublishProperties)
                        <select name="status" class="form-select">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($statusValue === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ $statusLabels[$statusValue] ?? ucfirst($statusValue) }}" disabled>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label">Zona</label>
                    <select name="zona_id" class="form-select">
                        <option value="">-- Selecciona una zona --</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}" @selected(old('zona_id', $property->zona_id ?? '') == $zona->id)>{{ $zona->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="owner-search" class="form-label">Propietario</label>
                    <div class="owner-combobox" data-owner-combobox data-search-url="{{ route('admin.propietarios.search') }}">
                        <input type="hidden" name="propietario_id" value="{{ $selectedPropietario?->id }}" data-owner-id>
                        <input
                            type="search"
                            id="owner-search"
                            class="form-control @error('propietario_id') is-invalid @enderror"
                            value="{{ $selectedPropietario?->nombre }}"
                            placeholder="Buscar por nombre, teléfono o email"
                            autocomplete="off"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="owner-search-results"
                            data-owner-search
                        >
                        <button type="button" class="owner-combobox-clear {{ $selectedPropietario ? '' : 'd-none' }}" aria-label="Quitar propietario" data-owner-clear>×</button>
                        <div id="owner-search-results" class="owner-combobox-results d-none" role="listbox" data-owner-results></div>
                    </div>
                    <div class="form-text"><a href="{{ route('admin.propietarios.index') }}" target="_blank" rel="noopener">Gestionar propietarios</a></div>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción (ES)</label>
                    <textarea name="description" class="form-control" rows="6">{{ old('description', $property->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="tab-pane fade" id="property-location" role="tabpanel" aria-labelledby="location-tab" tabindex="0">
<div class="d-grid gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Ubicación y mapa</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Población</label>
                    <input type="text" name="town" class="form-control" value="{{ old('town', $property->town ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Provincia</label>
                    <input type="text" name="province" class="form-control" value="{{ old('province', $property->province ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">País</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $property->country ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ubicación detallada</label>
                    <input type="text" name="location_detail" class="form-control" value="{{ old('location_detail', $property->location_detail ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Latitud</label>
                    <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $property->latitude ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitud</label>
                    <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $property->longitude ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="tab-pane fade" id="property-features" role="tabpanel" aria-labelledby="features-tab" tabindex="0">
<div class="d-grid gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Características y estado del inmueble</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Habitaciones</label>
                    <input type="number" name="habitaciones" class="form-control" value="{{ old('habitaciones', $property->bedrooms ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Baños</label>
                    <input type="number" name="banos" class="form-control" value="{{ old('banos', $property->bathrooms ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Metros construidos</label>
                    <input type="number" step="0.01" name="metros" class="form-control" value="{{ old('metros', $property->area ?? '') }}">
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="tiene_solar" class="form-check-input" value="1" id="solarCheck" @checked(old('tiene_solar', $property->tiene_solar ?? false))>
                        <label class="form-check-label" for="solarCheck">Tiene solar</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Metros del solar</label>
                    <input type="number" step="0.01" name="metros_solar" class="form-control" value="{{ old('metros_solar', $property->metros_solar ?? '') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    @if ($canPublishProperties)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="destacada" class="form-check-input" id="destacadaCheck" value="1" @checked(old('destacada', $property->is_featured ?? false))>
                            <label class="form-check-label" for="destacadaCheck">Propiedad destacada</label>
                        </div>
                    @endif
                </div>

                @php
                    $featureChecks = [
                        'tiene_patio' => 'Patio o jardín exterior',
                        'tiene_piscina' => 'Piscina',
                        'has_air_conditioning' => 'Aire acondicionado',
                        'has_garage' => 'Garaje',
                        'has_lift' => 'Ascensor',
                        'has_garden' => 'Jardín',
                        'has_terrace' => 'Terraza',
                        'has_sea_views' => 'Vistas al mar',
                        'has_parking' => 'Parking',
                        'is_furnished' => 'Amueblada',
                        'has_storage_room' => 'Trastero',
                        'has_solarium' => 'Solárium',
                        'part_ownership' => 'Propiedad compartida',
                        'leasehold' => 'Leasehold',
                        'new_build' => 'Obra nueva',
                    ];
                @endphp

                @foreach ($featureChecks as $field => $label)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="{{ $field }}" class="form-check-input" value="1" id="{{ $field }}" @checked(old($field, $property->{$field} ?? false))>
                            <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                        </div>
                    </div>
                @endforeach

                <div class="col-md-6">
                    <label class="form-label">Consumo energético</label>
                    <input type="text" name="energy_consumption" class="form-control" value="{{ old('energy_consumption', $property->energy_consumption ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Emisiones energéticas</label>
                    <input type="text" name="energy_emissions" class="form-control" value="{{ old('energy_emissions', $property->energy_emissions ?? '') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Features importadas o manuales</label>
                    <textarea name="features_text" class="form-control" rows="6" placeholder="Una feature por línea o separadas por comas">{{ $featureTextareaValue }}</textarea>
                    <div class="form-text">Se usarán también para mostrar mejor la ficha pública y completar ciertos indicadores.</div>
                </div>

                <div class="col-12">
                    <label class="form-label">Notas de origen / internas</label>
                    <textarea name="source_notes" class="form-control" rows="4">{{ old('source_notes', $property->source_notes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="tab-pane fade" id="property-media" role="tabpanel" aria-labelledby="media-tab" tabindex="0">
<div class="d-grid gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Enlaces multimedia</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Vídeo</label>
                    <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $property->video_url ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tour virtual</label>
                    <input type="url" name="virtual_tour_url" class="form-control" value="{{ old('virtual_tour_url', $property->virtual_tour_url ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="tab-pane fade" id="property-languages" role="tabpanel" aria-labelledby="languages-tab" tabindex="0">
<div class="d-grid gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Títulos y ubicaciones por idioma</h5>
            <div class="row g-3">
                @foreach ($propertyTranslationLocales as $locale => $label)
                    <div class="col-md-6">
                        <label class="form-label">Título {{ $label }}</label>
                        <input type="text" name="title_{{ $locale }}" class="form-control" value="{{ old('title_' . $locale, $property->{'title_' . $locale} ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ubicación {{ $label }}</label>
                        <input type="text" name="location_{{ $locale }}" class="form-control" value="{{ old('location_' . $locale, $property->{'location_' . $locale} ?? '') }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Descripciones por idioma</h5>
            <div class="row g-3">
                @foreach ($propertyTranslationLocales as $locale => $label)
                    <div class="col-md-6">
                        <label class="form-label">Descripción {{ $label }}</label>
                        <textarea name="description_{{ $locale }}" class="form-control" rows="6">{{ old('description_' . $locale, $property->{'description_' . $locale} ?? '') }}</textarea>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-2">Resumen rápido</h5>
            <p class="text-muted small mb-3">
                Si dejas estos campos vacíos, la ficha pública generará frases automáticas según las características de la vivienda.
            </p>

            <div class="row g-3">
                @foreach (['' => config('app.supported_locales.' . config('app.locale'))] + collect($propertyTranslationLocales)->mapWithKeys(fn ($label, $locale) => ['_' . $locale => $label])->all() as $suffix => $label)
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="col-md-4">
                            <label class="form-label">Resumen {{ $i }} {{ $label }}</label>
                            <input type="text" name="quick_summary_{{ $i }}{{ $suffix }}" class="form-control" value="{{ old('quick_summary_' . $i . $suffix, $property->{'quick_summary_' . $i . $suffix} ?? '') }}">
                        </div>
                    @endfor
                @endforeach
            </div>
        </div>
    </div>

</div>
</div>

<div class="tab-pane fade" id="property-images" role="tabpanel" aria-labelledby="images-tab" tabindex="0">
    <div class="d-grid gap-4">
        @if (isset($property) && $property->thumbnail)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Imagen principal actual</h5>
                    <div class="border rounded p-3 bg-light d-inline-block">
                        <img src="{{ asset('storage/' . $property->thumbnail) }}" alt="Imagen principal de {{ $property->title }}" class="img-fluid rounded" style="max-width: 240px; object-fit: cover;">
                    </div>
                </div>
            </div>
        @endif

        @if (isset($property) && $property->images->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Imágenes actuales</h5>
                    <div class="row g-3">
                        @foreach ($property->images as $image)
                            <div class="col-6 col-md-4 col-xl-3 position-relative" data-property-image="{{ $image->id }}">
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Imagen de {{ $property->title }}" class="img-fluid rounded w-100" style="object-fit: cover; aspect-ratio: 1/1;">
                                <button type="button" class="btn btn-sm btn-light border position-absolute bottom-0 start-0 m-2" data-set-thumbnail="{{ $image->id }}">
                                    Hacer principal
                                </button>
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" data-delete-image="{{ $image->id }}" aria-label="Eliminar imagen">
                                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <label class="form-label">{{ isset($property) ? 'Añadir nuevas imágenes' : 'Imágenes de la propiedad' }}</label>
                <div class="dropzone" id="dropzone">
                    Arrastra las imágenes aquí o haz clic para seleccionar
                    <input type="file" name="images[]" id="images" class="form-control d-none" accept="image/*" multiple>
                </div>
                <div class="form-text mt-2">
                    {{ isset($property) ? 'Las nuevas imágenes se añadirán al final.' : 'La primera imagen se usará como miniatura principal.' }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="d-flex justify-content-end border-top pt-3 mt-4">
    <button type="submit" class="btn btn-main px-4">Guardar propiedad</button>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.getElementById('propertyFormTabs');

            if (!tabs) {
                return;
            }

            const storageKey = 'property-form-tab:' + window.location.pathname;
            const validationErrors = @json($errors->messages());
            let firstInvalidField = null;

            Object.entries(validationErrors).forEach(function ([field, messages]) {
                let inputName = field.replace(/^description_extra\.([^.]*)$/, 'description_extra[$1]');

                if (/^images\.\d+$/.test(field)) {
                    inputName = 'images[]';
                }

                const input = field === 'propietario_id'
                    ? document.querySelector('[data-owner-search]')
                    : document.querySelector('[name="' + CSS.escape(inputName) + '"]');

                if (!input) {
                    return;
                }

                input.classList.add('is-invalid');

                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = messages[0];
                input.insertAdjacentElement('afterend', feedback);
                firstInvalidField ??= input;
            });

            let validationTabOpened = false;
            const propertyForm = tabs.closest('form');

            propertyForm?.addEventListener('invalid', function (event) {
                if (validationTabOpened) {
                    return;
                }

                const invalidPane = event.target.closest('.tab-pane');

                if (!invalidPane || invalidPane.classList.contains('active')) {
                    return;
                }

                const invalidTab = tabs.querySelector('[data-bs-target="#' + CSS.escape(invalidPane.id) + '"]');

                if (invalidTab) {
                    validationTabOpened = true;
                    invalidTab.click();
                    window.setTimeout(function () {
                        event.target.focus();
                        validationTabOpened = false;
                    }, 200);
                }
            }, true);

            const targetPane = firstInvalidField?.closest('.tab-pane');
            const rememberedTarget = sessionStorage.getItem(storageKey);
            const targetSelector = targetPane ? '#' + targetPane.id : rememberedTarget;
            const targetTab = targetSelector
                ? tabs.querySelector('[data-bs-target="' + CSS.escape(targetSelector) + '"]')
                : null;

            if (targetTab) {
                targetTab.click();
            }

            tabs.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tab) {
                tab.addEventListener('shown.bs.tab', function (event) {
                    sessionStorage.setItem(storageKey, event.target.dataset.bsTarget);
                });
            });

            const ownerCombobox = document.querySelector('[data-owner-combobox]');

            if (ownerCombobox) {
                const searchInput = ownerCombobox.querySelector('[data-owner-search]');
                const ownerIdInput = ownerCombobox.querySelector('[data-owner-id]');
                const resultsPanel = ownerCombobox.querySelector('[data-owner-results]');
                const clearButton = ownerCombobox.querySelector('[data-owner-clear]');
                let results = [];
                let activeIndex = -1;
                let debounceTimer = null;
                let requestController = null;

                function closeResults() {
                    resultsPanel.classList.add('d-none');
                    searchInput.setAttribute('aria-expanded', 'false');
                    activeIndex = -1;
                }

                function chooseOwner(owner) {
                    ownerIdInput.value = owner.id;
                    searchInput.value = owner.label;
                    clearButton.classList.remove('d-none');
                    closeResults();
                }

                function renderResults() {
                    resultsPanel.innerHTML = '';

                    if (results.length === 0) {
                        const empty = document.createElement('div');
                        empty.className = 'small text-muted p-2';
                        empty.textContent = 'No se han encontrado propietarios.';
                        resultsPanel.appendChild(empty);
                    } else {
                        results.forEach(function (owner, index) {
                            const option = document.createElement('button');
                            option.type = 'button';
                            option.className = 'owner-combobox-option';
                            option.setAttribute('role', 'option');

                            const name = document.createElement('span');
                            name.className = 'd-block fw-semibold';
                            name.textContent = owner.label;
                            option.appendChild(name);

                            if (owner.contact) {
                                const contact = document.createElement('span');
                                contact.className = 'd-block small text-muted';
                                contact.textContent = owner.contact;
                                option.appendChild(contact);
                            }

                            option.addEventListener('mousedown', event => event.preventDefault());
                            option.addEventListener('click', () => chooseOwner(owner));
                            resultsPanel.appendChild(option);
                        });
                    }

                    resultsPanel.classList.remove('d-none');
                    searchInput.setAttribute('aria-expanded', 'true');
                }

                function syncActiveOption() {
                    resultsPanel.querySelectorAll('.owner-combobox-option').forEach(function (option, index) {
                        option.classList.toggle('is-active', index === activeIndex);
                        option.setAttribute('aria-selected', index === activeIndex ? 'true' : 'false');
                    });
                }

                async function searchOwners() {
                    requestController?.abort();
                    requestController = new AbortController();
                    const url = new URL(ownerCombobox.dataset.searchUrl, window.location.origin);
                    url.searchParams.set('q', searchInput.value.trim());

                    try {
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                            signal: requestController.signal,
                        });

                        if (!response.ok) {
                            throw new Error('No se pudo buscar propietarios.');
                        }

                        const payload = await response.json();
                        results = payload.results || [];
                        activeIndex = -1;
                        renderResults();
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            results = [];
                            renderResults();
                        }
                    }
                }

                searchInput.addEventListener('focus', searchOwners);
                searchInput.addEventListener('input', function () {
                    ownerIdInput.value = '';
                    clearButton.classList.toggle('d-none', searchInput.value === '');
                    window.clearTimeout(debounceTimer);
                    debounceTimer = window.setTimeout(searchOwners, 180);
                });

                searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();

                        if (resultsPanel.classList.contains('d-none')) {
                            searchOwners();
                            return;
                        }

                        const direction = event.key === 'ArrowDown' ? 1 : -1;
                        activeIndex = Math.max(0, Math.min(results.length - 1, activeIndex + direction));
                        syncActiveOption();
                    } else if (event.key === 'Enter' && results.length > 0) {
                        event.preventDefault();
                        chooseOwner(results[activeIndex >= 0 ? activeIndex : 0]);
                    } else if (event.key === 'Escape') {
                        closeResults();
                    }
                });

                searchInput.addEventListener('blur', function () {
                    window.setTimeout(function () {
                        if (!ownerIdInput.value) {
                            searchInput.value = '';
                            clearButton.classList.add('d-none');
                        }

                        closeResults();
                    }, 120);
                });

                clearButton.addEventListener('click', function () {
                    ownerIdInput.value = '';
                    searchInput.value = '';
                    clearButton.classList.add('d-none');
                    searchInput.focus();
                });
            }
        });
    </script>
@endpush
