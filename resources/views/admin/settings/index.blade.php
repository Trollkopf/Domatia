@extends('layouts.admin')

@section('title', 'Configuracion')

@section('styles')
    <style>
        .settings-image-card {
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            background: #fff;
            padding: 1rem;
        }

        .settings-current-preview-frame,
        .settings-cropper-frame {
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            border: 1px dashed #cbd5e1;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.04), rgba(15, 23, 42, 0.08)),
                #f8fafc;
            aspect-ratio: 20 / 9;
        }

        .settings-current-preview-frame img,
        .settings-cropper-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .settings-cropper-image {
            position: absolute;
            top: 0;
            left: 0;
            width: auto;
            height: auto;
            max-width: none;
            touch-action: none;
            user-select: none;
            cursor: grab;
        }

        .settings-cropper-image.is-dragging {
            cursor: grabbing;
        }

        .settings-current-preview-empty,
        .settings-cropper-empty {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
            color: #64748b;
            font-size: 0.95rem;
        }

        .settings-cropper-guides {
            position: absolute;
            inset: 0;
            pointer-events: none;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
        }

        .settings-cropper-guides::before,
        .settings-cropper-guides::after {
            content: "";
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
        }

        .settings-cropper-guides::before {
            top: 0;
            bottom: 0;
            left: 33.333%;
            width: 1px;
            box-shadow: calc(33.333% + 1px) 0 0 rgba(255, 255, 255, 0.3);
        }

        .settings-cropper-guides::after {
            left: 0;
            right: 0;
            top: 33.333%;
            height: 1px;
            box-shadow: 0 calc(33.333% + 1px) 0 rgba(255, 255, 255, 0.3);
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

            document.querySelectorAll('[data-image-field]').forEach(function (field) {
                const select = field.querySelector('[data-image-select]');
                const selectedPreview = field.querySelector('[data-selected-preview]');
                const selectedEmpty = field.querySelector('[data-selected-empty]');
                const fileInput = field.querySelector('[data-image-upload]');
                const frame = field.querySelector('[data-image-frame]');
                const preview = field.querySelector('[data-image-preview]');
                const empty = field.querySelector('[data-image-empty]');
                const guides = field.querySelector('[data-image-guides]');
                const zoomInput = field.querySelector('[data-image-zoom]');
                const resetButton = field.querySelector('[data-image-reset]');
                const cropInput = field.querySelector('[data-image-crop-input]');
                const targetWidth = Number(field.dataset.targetWidth || 1600);
                const targetHeight = Number(field.dataset.targetHeight || 720);

                const state = {
                    imageWidth: 0,
                    imageHeight: 0,
                    zoom: 1,
                    offsetX: 0,
                    offsetY: 0,
                    dragging: false,
                    active: false,
                    pointerId: null,
                    startX: 0,
                    startY: 0,
                    startOffsetX: 0,
                    startOffsetY: 0,
                };

                function updateSelectedPreview() {
                    if (select.value) {
                        selectedPreview.src = select.value;
                        selectedPreview.classList.remove('d-none');
                        selectedEmpty.classList.add('d-none');
                    } else {
                        selectedPreview.src = '';
                        selectedPreview.classList.add('d-none');
                        selectedEmpty.classList.remove('d-none');
                    }
                }

                function getBounds() {
                    return frame.getBoundingClientRect();
                }

                function syncCropPayload() {
                    if (!state.active) {
                        cropInput.value = '';
                        return;
                    }

                    const bounds = getBounds();
                    cropInput.value = JSON.stringify({
                        zoom: Number(state.zoom.toFixed(4)),
                        offsetX: Number((state.offsetX * (targetWidth / bounds.width)).toFixed(2)),
                        offsetY: Number((state.offsetY * (targetHeight / bounds.height)).toFixed(2)),
                    });
                }

                function render() {
                    if (!state.active) {
                        return;
                    }

                    const bounds = getBounds();
                    const baseScale = Math.max(bounds.width / state.imageWidth, bounds.height / state.imageHeight);
                    const renderedWidth = state.imageWidth * baseScale * state.zoom;
                    const renderedHeight = state.imageHeight * baseScale * state.zoom;
                    const maxOffsetX = Math.max(0, (renderedWidth - bounds.width) / 2);
                    const maxOffsetY = Math.max(0, (renderedHeight - bounds.height) / 2);

                    state.offsetX = clamp(state.offsetX, -maxOffsetX, maxOffsetX);
                    state.offsetY = clamp(state.offsetY, -maxOffsetY, maxOffsetY);

                    preview.style.width = `${renderedWidth}px`;
                    preview.style.height = `${renderedHeight}px`;
                    preview.style.left = `${(bounds.width - renderedWidth) / 2 + state.offsetX}px`;
                    preview.style.top = `${(bounds.height - renderedHeight) / 2 + state.offsetY}px`;

                    syncCropPayload();
                }

                function clearUpload() {
                    state.active = false;
                    state.dragging = false;
                    state.pointerId = null;
                    state.zoom = 1;
                    state.offsetX = 0;
                    state.offsetY = 0;
                    preview.src = '';
                    preview.classList.add('d-none');
                    preview.classList.remove('is-dragging');
                    empty.classList.remove('d-none');
                    guides.classList.add('d-none');
                    zoomInput.value = '1';
                    zoomInput.disabled = true;
                    resetButton.disabled = true;
                    cropInput.value = '';
                    fileInput.value = '';
                }

                function loadImage(dataUrl) {
                    preview.onload = function () {
                        state.imageWidth = preview.naturalWidth;
                        state.imageHeight = preview.naturalHeight;
                        state.zoom = 1;
                        state.offsetX = 0;
                        state.offsetY = 0;
                        state.active = true;
                        preview.classList.remove('d-none');
                        empty.classList.add('d-none');
                        guides.classList.remove('d-none');
                        zoomInput.disabled = false;
                        resetButton.disabled = false;
                        zoomInput.value = '1';
                        render();
                    };

                    preview.src = dataUrl;
                }

                select.addEventListener('change', updateSelectedPreview);
                updateSelectedPreview();

                fileInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];

                    if (!file) {
                        clearUpload();
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (loadEvent) {
                        loadImage(loadEvent.target.result);
                    };
                    reader.readAsDataURL(file);
                });

                zoomInput.addEventListener('input', function () {
                    state.zoom = Number(zoomInput.value);
                    render();
                });

                resetButton.addEventListener('click', function () {
                    clearUpload();
                });

                preview.addEventListener('pointerdown', function (event) {
                    if (!state.active) {
                        return;
                    }

                    state.dragging = true;
                    state.pointerId = event.pointerId;
                    state.startX = event.clientX;
                    state.startY = event.clientY;
                    state.startOffsetX = state.offsetX;
                    state.startOffsetY = state.offsetY;
                    preview.classList.add('is-dragging');
                    preview.setPointerCapture(event.pointerId);
                });

                preview.addEventListener('pointermove', function (event) {
                    if (!state.dragging || state.pointerId !== event.pointerId) {
                        return;
                    }

                    state.offsetX = state.startOffsetX + (event.clientX - state.startX);
                    state.offsetY = state.startOffsetY + (event.clientY - state.startY);
                    render();
                });

                function stopDragging(event) {
                    if (state.pointerId !== event.pointerId) {
                        return;
                    }

                    state.dragging = false;
                    state.pointerId = null;
                    preview.classList.remove('is-dragging');
                }

                preview.addEventListener('pointerup', stopDragging);
                preview.addEventListener('pointercancel', stopDragging);

                window.addEventListener('resize', function () {
                    if (state.active) {
                        render();
                    }
                });
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sectionTabs = document.getElementById('settingsSectionTabs');
            const settingsForm = sectionTabs?.closest('form');

            if (!sectionTabs || !settingsForm) {
                return;
            }

            const storageKey = 'settings-section-tab';
            const validationErrors = @json($errors->messages());
            let firstInvalidField = null;

            function activateFieldSection(field) {
                const sectionPane = field.closest('.settings-section-pane');

                if (sectionPane) {
                    sectionTabs.querySelector('[data-bs-target="#' + CSS.escape(sectionPane.id) + '"]')?.click();
                }

                const localePane = field.closest('#settings-languages-pane .tab-pane');

                if (localePane) {
                    document.getElementById('settingsLocaleTabs')
                        ?.querySelector('[data-bs-target="#' + CSS.escape(localePane.id) + '"]')
                        ?.click();
                }
            }

            Object.entries(validationErrors).forEach(function ([field, messages]) {
                const input = settingsForm.querySelector('[name="' + CSS.escape(field) + '"]');

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

            if (firstInvalidField) {
                activateFieldSection(firstInvalidField);
            } else {
                const rememberedTarget = sessionStorage.getItem(storageKey);
                sectionTabs.querySelector('[data-bs-target="' + CSS.escape(rememberedTarget || '') + '"]')?.click();
            }

            let validationTabOpened = false;

            settingsForm.addEventListener('invalid', function (event) {
                if (validationTabOpened) {
                    return;
                }

                validationTabOpened = true;
                activateFieldSection(event.target);

                window.setTimeout(function () {
                    event.target.focus();
                    validationTabOpened = false;
                }, 200);
            }, true);

            sectionTabs.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tab) {
                tab.addEventListener('shown.bs.tab', function (event) {
                    sessionStorage.setItem(storageKey, event.target.dataset.bsTarget);
                });
            });
        });
    </script>
@endpush

@section('content')
    @php
        $localizedTextFields = [
            'home_hero_badge' => ['label' => 'Etiqueta superior hero', 'type' => 'text'],
            'home_hero_title' => ['label' => 'Titulo hero', 'type' => 'text'],
            'home_hero_subtitle' => ['label' => 'Subtitulo hero', 'type' => 'text'],
            'home_search_button_text' => ['label' => 'Texto boton buscador', 'type' => 'text'],
            'home_value_1' => ['label' => 'Argumento 1', 'type' => 'text'],
            'home_value_2' => ['label' => 'Argumento 2', 'type' => 'text'],
            'home_value_3' => ['label' => 'Argumento 3', 'type' => 'text'],
            'home_featured_heading' => ['label' => 'Titulo destacadas', 'type' => 'text'],
            'home_featured_subtitle' => ['label' => 'Subtitulo destacadas', 'type' => 'text'],
            'home_cta_heading' => ['label' => 'Titulo bloque final', 'type' => 'text'],
            'home_cta_body' => ['label' => 'Texto bloque final', 'type' => 'textarea', 'rows' => 4],
            'home_cta_primary_text' => ['label' => 'Texto CTA principal', 'type' => 'text'],
            'home_cta_secondary_text' => ['label' => 'Texto CTA secundario', 'type' => 'text'],
            'contact_intro' => ['label' => 'Texto de introduccion en contacto', 'type' => 'textarea', 'rows' => 3],
            'about_heading' => ['label' => 'Titulo seccion Sobre nosotros', 'type' => 'text'],
            'about_body' => ['label' => 'Texto principal Sobre nosotros', 'type' => 'textarea', 'rows' => 6],
            'about_header_title' => ['label' => 'Titulo header Sobre nosotros', 'type' => 'text'],
            'contact_header_title' => ['label' => 'Titulo header Contacto', 'type' => 'text'],
            'environment_header_title' => ['label' => 'Titulo header Entorno', 'type' => 'text'],
            'register_header_title' => ['label' => 'Titulo header Registro', 'type' => 'text'],
            'footer_text' => ['label' => 'Texto de footer', 'type' => 'text'],
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Configuracion</h1>
            <p class="text-muted mb-0">Gestiona datos base de la empresa y los textos clave del sitio.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-1">Revisa los campos indicados antes de guardar.</div>
                        <div class="small">Se ha abierto automáticamente la sección con el primer error.</div>
                    </div>
                @endif

                <ul class="nav admin-form-tabs mb-4" id="settingsSectionTabs" role="tablist" aria-label="Secciones de ajustes">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="settings-general-tab" data-bs-toggle="tab" data-bs-target="#settings-general-pane" type="button" role="tab" aria-controls="settings-general-pane" aria-selected="true">General</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-seo-tab" data-bs-toggle="tab" data-bs-target="#settings-seo-pane" type="button" role="tab" aria-controls="settings-seo-pane" aria-selected="false">SEO</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-home-tab" data-bs-toggle="tab" data-bs-target="#settings-home-pane" type="button" role="tab" aria-controls="settings-home-pane" aria-selected="false">Portada</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-content-tab" data-bs-toggle="tab" data-bs-target="#settings-content-pane" type="button" role="tab" aria-controls="settings-content-pane" aria-selected="false">Contenido</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="settings-headers-tab" data-bs-toggle="tab" data-bs-target="#settings-headers-pane" type="button" role="tab" aria-controls="settings-headers-pane" aria-selected="false">Cabeceras</button>
                    </li>
                    @if (! empty($settingsLocales))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settings-languages-tab" data-bs-toggle="tab" data-bs-target="#settings-languages-pane" type="button" role="tab" aria-controls="settings-languages-pane" aria-selected="false">Idiomas</button>
                        </li>
                    @endif
                </ul>

                <div class="tab-content" id="settingsSectionTabContent">
                <div class="tab-pane settings-section-pane fade show active" id="settings-general-pane" role="tabpanel" aria-labelledby="settings-general-tab" tabindex="0">
                <div class="row g-3">
                    <div class="col-12">
                        <h2 class="h5 mb-1">Datos generales</h2>
                        <p class="text-muted small mb-0">Se reutilizan en navegacion, contacto y footer.</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nombre de empresa</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email comercial</label>
                        <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Direccion</label>
                        <input type="text" name="company_address" class="form-control" value="{{ old('company_address', $settings['company_address']) }}">
                    </div>

                </div>
                </div>

                <div class="tab-pane settings-section-pane fade" id="settings-seo-pane" role="tabpanel" aria-labelledby="settings-seo-tab" tabindex="0">
                <div class="row g-3">

                    <div class="col-12">
                        <h2 class="h5 mb-1">SEO tecnico</h2>
                        <p class="text-muted small mb-0">Base global para titles, descripciones, verificaciones y robots.</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sufijo global de title</label>
                        <input type="text" name="seo_title_suffix" class="form-control" value="{{ old('seo_title_suffix', $settings['seo_title_suffix']) }}">
                        <div class="form-text">Se anade al final del title cuando la pagina no lo incluye ya. Ejemplo: Domatia.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Crawl delay</label>
                        <input type="number" name="seo_crawl_delay" class="form-control" min="1" max="30" value="{{ old('seo_crawl_delay', $settings['seo_crawl_delay']) }}">
                        <div class="form-text">Opcional. Se publica en robots.txt para bots que lo respetan.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripcion SEO por defecto</label>
                        <textarea name="seo_default_description" class="form-control" rows="3">{{ old('seo_default_description', $settings['seo_default_description']) }}</textarea>
                        <div class="form-text">Se usa cuando una pagina no tenga una descripcion mas especifica. Mejor entre 140 y 160 caracteres.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Google site verification</label>
                        <input type="text" name="seo_google_verification" class="form-control" value="{{ old('seo_google_verification', $settings['seo_google_verification']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Bing site verification</label>
                        <input type="text" name="seo_bing_verification" class="form-control" value="{{ old('seo_bing_verification', $settings['seo_bing_verification']) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto de footer</label>
                        <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', $settings['footer_text']) }}">
                    </div>

                </div>
                </div>

                <div class="tab-pane settings-section-pane fade" id="settings-home-pane" role="tabpanel" aria-labelledby="settings-home-tab" tabindex="0">
                <div class="row g-3">

                    <div class="col-12">
                        <h2 class="h5 mb-1">Portada</h2>
                        <p class="text-muted small mb-0">Controla la propuesta de valor visible en la home.</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Cantidad de heroes en la home</label>
                        <select name="home_hero_count" class="form-select">
                            <option value="1" @selected(old('home_hero_count', $settings['home_hero_count']) == '1')>1 hero</option>
                            <option value="2" @selected(old('home_hero_count', $settings['home_hero_count']) == '2')>2 heroes</option>
                            <option value="3" @selected(old('home_hero_count', $settings['home_hero_count']) == '3')>3 heroes</option>
                        </select>
                        <div class="form-text">Se mostraran las primeras imagenes configuradas en este bloque.</div>
                    </div>

                    <div class="col-12"></div>

                    @include('admin.settings._image-field', [
                        'name' => 'home_hero_image_1',
                        'label' => 'Imagen hero 1',
                        'value' => $settings['home_hero_image_1'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                    @include('admin.settings._image-field', [
                        'name' => 'home_hero_image_2',
                        'label' => 'Imagen hero 2',
                        'value' => $settings['home_hero_image_2'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                    @include('admin.settings._image-field', [
                        'name' => 'home_hero_image_3',
                        'label' => 'Imagen hero 3',
                        'value' => $settings['home_hero_image_3'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                    <div class="col-md-6">
                        <label class="form-label">Etiqueta superior hero</label>
                        <input type="text" name="home_hero_badge" class="form-control" value="{{ old('home_hero_badge', $settings['home_hero_badge']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo hero</label>
                        <input type="text" name="home_hero_title" class="form-control" value="{{ old('home_hero_title', $settings['home_hero_title']) }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Subtitulo hero</label>
                        <input type="text" name="home_hero_subtitle" class="form-control" value="{{ old('home_hero_subtitle', $settings['home_hero_subtitle']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Texto boton buscador</label>
                        <input type="text" name="home_search_button_text" class="form-control" value="{{ old('home_search_button_text', $settings['home_search_button_text']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Argumento 1</label>
                        <input type="text" name="home_value_1" class="form-control" value="{{ old('home_value_1', $settings['home_value_1']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Argumento 2</label>
                        <input type="text" name="home_value_2" class="form-control" value="{{ old('home_value_2', $settings['home_value_2']) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Argumento 3</label>
                        <input type="text" name="home_value_3" class="form-control" value="{{ old('home_value_3', $settings['home_value_3']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo destacadas</label>
                        <input type="text" name="home_featured_heading" class="form-control" value="{{ old('home_featured_heading', $settings['home_featured_heading']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Subtitulo destacadas</label>
                        <input type="text" name="home_featured_subtitle" class="form-control" value="{{ old('home_featured_subtitle', $settings['home_featured_subtitle']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo bloque final</label>
                        <input type="text" name="home_cta_heading" class="form-control" value="{{ old('home_cta_heading', $settings['home_cta_heading']) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto bloque final</label>
                        <textarea name="home_cta_body" class="form-control" rows="4">{{ old('home_cta_body', $settings['home_cta_body']) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Texto CTA principal</label>
                        <input type="text" name="home_cta_primary_text" class="form-control" value="{{ old('home_cta_primary_text', $settings['home_cta_primary_text']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL CTA principal</label>
                        <input type="text" name="home_cta_primary_url" class="form-control" value="{{ old('home_cta_primary_url', $settings['home_cta_primary_url']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Texto CTA secundario</label>
                        <input type="text" name="home_cta_secondary_text" class="form-control" value="{{ old('home_cta_secondary_text', $settings['home_cta_secondary_text']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">URL CTA secundario</label>
                        <input type="text" name="home_cta_secondary_url" class="form-control" value="{{ old('home_cta_secondary_url', $settings['home_cta_secondary_url']) }}">
                    </div>

                </div>
                </div>

                <div class="tab-pane settings-section-pane fade" id="settings-content-pane" role="tabpanel" aria-labelledby="settings-content-tab" tabindex="0">
                <div class="row g-3">

                    <div class="col-12">
                        <h2 class="h5 mb-1">Paginas de contenido</h2>
                        <p class="text-muted small mb-0">Textos reutilizados para contacto y sobre nosotros.</p>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto de introduccion en contacto</label>
                        <textarea name="contact_intro" class="form-control" rows="3">{{ old('contact_intro', $settings['contact_intro']) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo seccion "Sobre nosotros"</label>
                        <input type="text" name="about_heading" class="form-control" value="{{ old('about_heading', $settings['about_heading']) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Texto principal "Sobre nosotros"</label>
                        <textarea name="about_body" class="form-control" rows="6">{{ old('about_body', $settings['about_body']) }}</textarea>
                    </div>

                </div>
                </div>

                <div class="tab-pane settings-section-pane fade" id="settings-headers-pane" role="tabpanel" aria-labelledby="settings-headers-tab" tabindex="0">
                <div class="row g-3">

                    <div class="col-12">
                        <h2 class="h5 mb-1">Headers de secciones</h2>
                        <p class="text-muted small mb-0">Controla el titulo y la imagen principal de cada portada publica.</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Titulo header "Sobre nosotros"</label>
                        <input type="text" name="about_header_title" class="form-control" value="{{ old('about_header_title', $settings['about_header_title']) }}">
                    </div>

                    @include('admin.settings._image-field', [
                        'name' => 'about_header_image',
                        'label' => 'Imagen header "Sobre nosotros"',
                        'value' => $settings['about_header_image'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                    <div class="col-md-6">
                        <label class="form-label">Titulo header "Contacto"</label>
                        <input type="text" name="contact_header_title" class="form-control" value="{{ old('contact_header_title', $settings['contact_header_title']) }}">
                    </div>

                    @include('admin.settings._image-field', [
                        'name' => 'contact_header_image',
                        'label' => 'Imagen header "Contacto"',
                        'value' => $settings['contact_header_image'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                    <div class="col-md-6">
                        <label class="form-label">Titulo header "Entorno"</label>
                        <input type="text" name="environment_header_title" class="form-control" value="{{ old('environment_header_title', $settings['environment_header_title']) }}">
                    </div>

                    @include('admin.settings._image-field', [
                        'name' => 'environment_header_image',
                        'label' => 'Imagen header "Entorno"',
                        'value' => $settings['environment_header_image'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                    <div class="col-md-6">
                        <label class="form-label">Titulo header "Registro"</label>
                        <input type="text" name="register_header_title" class="form-control" value="{{ old('register_header_title', $settings['register_header_title']) }}">
                    </div>

                    @include('admin.settings._image-field', [
                        'name' => 'register_header_image',
                        'label' => 'Imagen header "Registro"',
                        'value' => $settings['register_header_image'],
                        'options' => $headerImageOptions,
                        'recommendedWidth' => $headerTargetWidth,
                        'recommendedHeight' => $headerTargetHeight,
                    ])

                </div>
                </div>

                    @if (! empty($settingsLocales))
                <div class="tab-pane settings-section-pane fade" id="settings-languages-pane" role="tabpanel" aria-labelledby="settings-languages-tab" tabindex="0">
                <div class="row g-3">

                        <div class="col-12">
                            <h2 class="h5 mb-1">Textos por idioma</h2>
                            <p class="text-muted small mb-0">
                                El castellano se sigue editando en los bloques superiores. Aqui puedes cargar overrides para cada idioma y, si dejas un campo vacio, se usara el texto por defecto.
                            </p>
                        </div>

                        <div class="col-12">
                            <ul class="nav admin-form-tabs mb-3" id="settingsLocaleTabs" role="tablist" aria-label="Idioma de los ajustes">
                                @foreach ($settingsLocales as $localeCode => $localeLabel)
                                    <li class="nav-item" role="presentation">
                                        <button
                                            class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="settings-tab-{{ $localeCode }}"
                                            data-bs-toggle="pill"
                                            data-bs-target="#settings-pane-{{ $localeCode }}"
                                            type="button"
                                            role="tab"
                                            aria-controls="settings-pane-{{ $localeCode }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                        >
                                            {{ $localeLabel }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content border rounded-4 p-3 p-lg-4 bg-light-subtle">
                                @foreach ($settingsLocales as $localeCode => $localeLabel)
                                    <div
                                        class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="settings-pane-{{ $localeCode }}"
                                        role="tabpanel"
                                        aria-labelledby="settings-tab-{{ $localeCode }}"
                                    >
                                        <div class="mb-3">
                                            <h3 class="h6 mb-1">{{ $localeLabel }}</h3>
                                            <p class="text-muted small mb-0">Overrides de contenido para la interfaz publica en {{ $localeLabel }}.</p>
                                        </div>

                                        <div class="row g-3">
                                            @foreach ($localizedTextFields as $fieldKey => $fieldConfig)
                                                <div class="col-12 {{ ($fieldConfig['type'] ?? 'text') === 'textarea' ? '' : 'col-md-6' }}">
                                                    <label class="form-label">{{ $fieldConfig['label'] }}</label>

                                                    @if (($fieldConfig['type'] ?? 'text') === 'textarea')
                                                        <textarea
                                                            name="{{ $fieldKey }}_{{ $localeCode }}"
                                                            class="form-control"
                                                            rows="{{ $fieldConfig['rows'] ?? 4 }}"
                                                        >{{ old($fieldKey . '_' . $localeCode, $localizedSettings[$localeCode][$fieldKey] ?? '') }}</textarea>
                                                    @else
                                                        <input
                                                            type="text"
                                                            name="{{ $fieldKey }}_{{ $localeCode }}"
                                                            class="form-control"
                                                            value="{{ old($fieldKey . '_' . $localeCode, $localizedSettings[$localeCode][$fieldKey] ?? '') }}"
                                                        >
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                </div>
                </div>
                    @endif
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-main">Guardar ajustes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
