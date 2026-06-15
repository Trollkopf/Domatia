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
@endphp

<div class="row">
    <div class="mb-3 col-md-6">
        <label class="form-label">Título base (ES)</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $property->title ?? '') }}" required>
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Ubicación base (ES)</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $property->location ?? '') }}">
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Precio</label>
        <input type="number" name="price" class="form-control" value="{{ old('price', $property->price ?? '') }}">
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Tipo</label>
        <input type="text" name="tipo" class="form-control" value="{{ old('tipo', $property->tipo ?? '') }}">
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Estado</label>
        @if ($canPublishProperties)
            <select name="status" class="form-select">
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected($statusValue === $value)>{{ $label }}</option>
                @endforeach
            </select>
        @else
            <input type="text" class="form-control" value="{{ $statusLabels[$statusValue] ?? ucfirst($statusValue) }}" disabled>
            <div class="form-text">
                {{ isset($property) ? 'Tu grupo puede editar la ficha, pero no cambiar su estado de publicación.' : 'Las nuevas propiedades se guardarán como borrador hasta que un perfil con permiso de publicación las revise.' }}
            </div>
        @endif
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Zona</label>
        <select name="zona_id" class="form-select">
            <option value="">-- Selecciona una zona --</option>
            @foreach ($zonas as $zona)
                <option value="{{ $zona->id }}" {{ old('zona_id', $property->zona_id ?? '') == $zona->id ? 'selected' : '' }}>
                    {{ $zona->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Títulos y ubicaciones por idioma</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Título EN</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $property->title_en ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ubicación EN</label>
                        <input type="text" name="location_en" class="form-control" value="{{ old('location_en', $property->location_en ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Título FR</label>
                        <input type="text" name="title_fr" class="form-control" value="{{ old('title_fr', $property->title_fr ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ubicación FR</label>
                        <input type="text" name="location_fr" class="form-control" value="{{ old('location_fr', $property->location_fr ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Título DE</label>
                        <input type="text" name="title_de" class="form-control" value="{{ old('title_de', $property->title_de ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ubicación DE</label>
                        <input type="text" name="location_de" class="form-control" value="{{ old('location_de', $property->location_de ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Título RU</label>
                        <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $property->title_ru ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ubicación RU</label>
                        <input type="text" name="location_ru" class="form-control" value="{{ old('location_ru', $property->location_ru ?? '') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Propietario</label>
        <select name="propietario_id" class="form-select">
            <option value="">-- Selecciona un propietario --</option>
            @foreach ($propietarios as $p)
                <option value="{{ $p->id }}" {{ old('propietario_id', $property->propietario_id ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-4">
        <label class="form-label">Habitaciones</label>
        <input type="number" name="habitaciones" class="form-control" value="{{ old('habitaciones', $property->bedrooms ?? '') }}">
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Baños</label>
        <input type="number" name="banos" class="form-control" value="{{ old('banos', $property->bathrooms ?? '') }}">
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Metros construidos</label>
        <input type="number" step="0.01" name="metros" class="form-control" value="{{ old('metros', $property->area ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="row">
        <div class="mb-3 col-md-4">
            <div class="form-check">
                <input type="checkbox" name="tiene_solar" class="form-check-input" value="1" id="solarCheck" {{ old('tiene_solar', $property->tiene_solar ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="solarCheck">Tiene solar</label>
            </div>
        </div>

        <div class="mb-3 col-md-4">
            <label class="form-label">Metros del solar</label>
            <input type="number" step="0.01" name="metros_solar" class="form-control" value="{{ old('metros_solar', $property->metros_solar ?? '') }}">
        </div>
    </div>

    <div class="mb-3 col-md-2">
        <div class="form-check">
            <input type="checkbox" name="tiene_patio" class="form-check-input" value="1" id="patioCheck" {{ old('tiene_patio', $property->tiene_patio ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="patioCheck">Patio</label>
        </div>
    </div>

    <div class="mb-3 col-md-2">
        <div class="form-check">
            <input type="checkbox" name="tiene_piscina" class="form-check-input" value="1" id="piscinaCheck" {{ old('tiene_piscina', $property->tiene_piscina ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="piscinaCheck">Piscina</label>
        </div>
    </div>

    <div class="mb-3 col-md-4">
        @if ($canPublishProperties)
            <div class="form-check">
                <input type="checkbox" name="destacada" class="form-check-input" id="destacadaCheck" value="1" {{ old('destacada', $property->is_featured ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="destacadaCheck">Propiedad destacada</label>
            </div>
        @else
            <label class="form-label d-block">Destacada</label>
            <span class="badge {{ ($property->is_featured ?? false) ? 'bg-success' : 'bg-secondary' }}">
                {{ ($property->is_featured ?? false) ? 'Sí' : 'No' }}
            </span>
            <div class="form-text">Solo los perfiles con permiso de publicación pueden cambiar este ajuste.</div>
        @endif
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Descripción (ES)</label>
    <textarea name="description" class="form-control">{{ old('description', $property->description ?? '') }}</textarea>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="mb-2">Resumen rápido</h5>
        <p class="text-muted small mb-3">
            Si dejas estos campos vacíos, la ficha pública generará frases automáticas según las características de la vivienda.
        </p>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Resumen 1 (ES)</label>
                <input type="text" name="quick_summary_1" class="form-control" value="{{ old('quick_summary_1', $property->quick_summary_1 ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 2 (ES)</label>
                <input type="text" name="quick_summary_2" class="form-control" value="{{ old('quick_summary_2', $property->quick_summary_2 ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 3 (ES)</label>
                <input type="text" name="quick_summary_3" class="form-control" value="{{ old('quick_summary_3', $property->quick_summary_3 ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Resumen 1 EN</label>
                <input type="text" name="quick_summary_1_en" class="form-control" value="{{ old('quick_summary_1_en', $property->quick_summary_1_en ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 2 EN</label>
                <input type="text" name="quick_summary_2_en" class="form-control" value="{{ old('quick_summary_2_en', $property->quick_summary_2_en ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 3 EN</label>
                <input type="text" name="quick_summary_3_en" class="form-control" value="{{ old('quick_summary_3_en', $property->quick_summary_3_en ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Resumen 1 FR</label>
                <input type="text" name="quick_summary_1_fr" class="form-control" value="{{ old('quick_summary_1_fr', $property->quick_summary_1_fr ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 2 FR</label>
                <input type="text" name="quick_summary_2_fr" class="form-control" value="{{ old('quick_summary_2_fr', $property->quick_summary_2_fr ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 3 FR</label>
                <input type="text" name="quick_summary_3_fr" class="form-control" value="{{ old('quick_summary_3_fr', $property->quick_summary_3_fr ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Resumen 1 DE</label>
                <input type="text" name="quick_summary_1_de" class="form-control" value="{{ old('quick_summary_1_de', $property->quick_summary_1_de ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 2 DE</label>
                <input type="text" name="quick_summary_2_de" class="form-control" value="{{ old('quick_summary_2_de', $property->quick_summary_2_de ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 3 DE</label>
                <input type="text" name="quick_summary_3_de" class="form-control" value="{{ old('quick_summary_3_de', $property->quick_summary_3_de ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Resumen 1 RU</label>
                <input type="text" name="quick_summary_1_ru" class="form-control" value="{{ old('quick_summary_1_ru', $property->quick_summary_1_ru ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 2 RU</label>
                <input type="text" name="quick_summary_2_ru" class="form-control" value="{{ old('quick_summary_2_ru', $property->quick_summary_2_ru ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Resumen 3 RU</label>
                <input type="text" name="quick_summary_3_ru" class="form-control" value="{{ old('quick_summary_3_ru', $property->quick_summary_3_ru ?? '') }}">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripción (EN)</label>
        <textarea name="description_en" class="form-control">{{ old('description_en', $property->description_en ?? '') }}</textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripción (FR)</label>
        <textarea name="description_fr" class="form-control">{{ old('description_fr', $property->description_fr ?? '') }}</textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripción (DE)</label>
        <textarea name="description_de" class="form-control">{{ old('description_de', $property->description_de ?? '') }}</textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripción (RU)</label>
        <textarea name="description_ru" class="form-control">{{ old('description_ru', $property->description_ru ?? '') }}</textarea>
    </div>
</div>

@if (!isset($property))
    <div class="mb-4">
        <label class="form-label">Imágenes de la propiedad</label>
        <div class="dropzone" id="dropzone">
            Arrastra las imágenes aquí o haz clic para seleccionar
            <input type="file" name="images[]" id="images" class="form-control d-none" multiple>
        </div>
        <div class="form-text mt-2">La primera imagen se usará como miniatura principal.</div>
    </div>
@endif

<div class="text-end mt-4">
    <button type="submit" class="btn btn-main">Guardar</button>
</div>
