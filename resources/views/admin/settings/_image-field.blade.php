@php
    $fieldId = str_replace('_', '-', $name);
    $currentValue = old($name, $value ?? '');
@endphp

<div class="col-12">
    <div class="settings-image-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <label class="form-label fw-semibold mb-1" for="{{ $fieldId }}">{{ $label }}</label>
                <p class="text-muted small mb-0">
                    Medida recomendada: {{ $recommendedWidth }} x {{ $recommendedHeight }} px.
                    El recorte se guarda adaptado automaticamente al formato del header.
                </p>
            </div>

            @if ($currentValue)
                <span class="badge text-bg-light border">Imagen activa</span>
            @endif
        </div>

        <div class="row g-3 align-items-start" data-image-field data-target-width="{{ $recommendedWidth }}"
            data-target-height="{{ $recommendedHeight }}">
            <div class="col-lg-4">
                <label class="form-label" for="{{ $fieldId }}">Usar imagen existente</label>
                <select name="{{ $name }}" id="{{ $fieldId }}" class="form-select" data-image-select>
                    <option value="">Sin imagen</option>
                    @foreach ($options as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" @selected($currentValue === $optionValue)>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </select>

                <div class="settings-current-preview mt-3">
                    <div class="small text-muted mb-2">Previsualizacion actual</div>
                    <div class="settings-current-preview-frame">
                        <img src="{{ $currentValue ?: '' }}" alt="{{ $label }}" data-selected-preview
                            class="{{ $currentValue ? '' : 'd-none' }}">
                        <div class="settings-current-preview-empty {{ $currentValue ? 'd-none' : '' }}" data-selected-empty>
                            No hay imagen seleccionada
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <label class="form-label">O subir una nueva</label>
                <div class="settings-cropper-frame" data-image-frame>
                    <img alt="{{ $label }}" class="settings-cropper-image d-none" data-image-preview>
                    <div class="settings-cropper-empty" data-image-empty>
                        Sube una imagen horizontal y arrastrala dentro del marco para ajustar el encuadre.
                    </div>
                    <div class="settings-cropper-guides d-none" data-image-guides></div>
                </div>

                <div class="row g-3 mt-1 align-items-center">
                    <div class="col-md-6">
                        <input type="file" name="{{ $name }}_upload" class="form-control" accept="image/png,image/jpeg,image/webp"
                            data-image-upload>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Zoom del recorte</label>
                        <input type="range" min="1" max="3" step="0.01" value="1" class="form-range"
                            data-image-zoom disabled>
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100" data-image-reset disabled>
                            Limpiar
                        </button>
                    </div>
                </div>

                <input type="hidden" name="{{ $name }}_crop" value="{{ old($name . '_crop') }}" data-image-crop-input>

                @error($name)
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror

                @error($name . '_upload')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
