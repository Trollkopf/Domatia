@csrf

<div class="row">
    <div class="mb-3 col-md-6">
        <label class="form-label">Titulo</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $property->title ?? '') }}"
            required>
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Ubicacion</label>
        <input type="text" name="location" class="form-control"
            value="{{ old('location', $property->location ?? '') }}">
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
        <select name="status" class="form-select">
            <option value="draft" {{ old('status', $property->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Borrador</option>
            <option value="published" {{ old('status', $property->status ?? 'draft') === 'published' ? 'selected' : '' }}>Publicada</option>
            <option value="reserved" {{ old('status', $property->status ?? 'draft') === 'reserved' ? 'selected' : '' }}>Reservada</option>
            <option value="sold" {{ old('status', $property->status ?? 'draft') === 'sold' ? 'selected' : '' }}>Vendida</option>
            <option value="hidden" {{ old('status', $property->status ?? 'draft') === 'hidden' ? 'selected' : '' }}>Oculta</option>
        </select>
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Zona</label>
        <select name="zona_id" class="form-select">
            <option value="">-- Selecciona una zona --</option>
            @foreach ($zonas as $zona)
                <option value="{{ $zona->id }}"
                    {{ old('zona_id', $property->zona_id ?? '') == $zona->id ? 'selected' : '' }}>
                    {{ $zona->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Propietario</label>
        <select name="propietario_id" class="form-select">
            <option value="">-- Selecciona un propietario --</option>
            @foreach ($propietarios as $p)
                <option value="{{ $p->id }}"
                    {{ old('propietario_id', $property->propietario_id ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-4">
        <label class="form-label">Habitaciones</label>
        <input type="number" name="habitaciones" class="form-control"
            value="{{ old('habitaciones', $property->bedrooms ?? '') }}">
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Banos</label>
        <input type="number" name="banos" class="form-control"
            value="{{ old('banos', $property->bathrooms ?? '') }}">
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Metros construidos</label>
        <input type="number" step="0.01" name="metros" class="form-control"
            value="{{ old('metros', $property->area ?? '') }}">
    </div>
</div>

<div class="row">
    <div class="row">
        <div class="mb-3 col-md-4">
            <div class="form-check">
                <input type="checkbox" name="tiene_solar" class="form-check-input" value="1" id="solarCheck"
                    {{ old('tiene_solar', $property->tiene_solar ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="solarCheck">Tiene solar</label>
            </div>
        </div>

        <div class="mb-3 col-md-4">
            <label class="form-label">Metros del solar</label>
            <input type="number" step="0.01" name="metros_solar" class="form-control"
                value="{{ old('metros_solar', $property->metros_solar ?? '') }}">
        </div>
    </div>

    <div class="mb-3 col-md-2">
        <div class="form-check">
            <input type="checkbox" name="tiene_patio" class="form-check-input" value="1" id="patioCheck"
                {{ old('tiene_patio', $property->tiene_patio ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="patioCheck">Patio</label>
        </div>
    </div>

    <div class="mb-3 col-md-2">
        <div class="form-check">
            <input type="checkbox" name="tiene_piscina" class="form-check-input" value="1" id="piscinaCheck"
                {{ old('tiene_piscina', $property->tiene_piscina ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="piscinaCheck">Piscina</label>
        </div>
    </div>
    <div class="mb-3 col-md-4">
        <div class="form-check">
            <input type="checkbox" name="destacada" class="form-check-input" id="destacadaCheck" value="1"
                {{ old('destacada', $property->is_featured ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="destacadaCheck">Propiedad destacada</label>
        </div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Descripcion (ES)</label>
    <textarea name="description" class="form-control">{{ old('description', $property->description ?? '') }}</textarea>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="mb-2">Resumen rapido</h5>
        <p class="text-muted small mb-3">
            Si dejas estos campos vacios, la ficha publica generara frases automaticas segun las caracteristicas de la vivienda.
        </p>

        <div class="row">
            <div class="mb-3 col-md-4">
                <label class="form-label">Resumen 1</label>
                <input type="text" name="quick_summary_1" class="form-control"
                    value="{{ old('quick_summary_1', $property->quick_summary_1 ?? '') }}"
                    placeholder="Ej. Chalet con piscina y 4 dormitorios">
            </div>
            <div class="mb-3 col-md-4">
                <label class="form-label">Resumen 2</label>
                <input type="text" name="quick_summary_2" class="form-control"
                    value="{{ old('quick_summary_2', $property->quick_summary_2 ?? '') }}"
                    placeholder="Ej. 240 m2 construidos y parcela de 800 m2">
            </div>
            <div class="mb-3 col-md-4">
                <label class="form-label">Resumen 3</label>
                <input type="text" name="quick_summary_3" class="form-control"
                    value="{{ old('quick_summary_3', $property->quick_summary_3 ?? '') }}"
                    placeholder="Ej. Lista para visita con referencia R-123">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripcion (EN)</label>
        <textarea name="description_en" class="form-control">{{ old('description_en', $property->description_en ?? '') }}</textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripcion (FR)</label>
        <textarea name="description_fr" class="form-control">{{ old('description_fr', $property->description_fr ?? '') }}</textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripcion (DE)</label>
        <textarea name="description_de" class="form-control">{{ old('description_de', $property->description_de ?? '') }}</textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label class="form-label">Descripcion (RU)</label>
        <textarea name="description_ru" class="form-control">{{ old('description_ru', $property->description_ru ?? '') }}</textarea>
    </div>
</div>

{{-- Solo en create: zona de imagenes --}}
@if (!isset($property))
    <div class="mb-4">
        <label class="form-label">Imagenes de la propiedad</label>
        <div class="dropzone" id="dropzone">
            Arrastra las imagenes aqui o haz clic para seleccionar
            <input type="file" name="images[]" id="images" class="form-control d-none" multiple>
        </div>
        <div class="form-text mt-2">La primera imagen se usara como miniatura principal (thumbnail).</div>
    </div>
@endif

<div class="text-end mt-4">
    <button type="submit" class="btn btn-main">Guardar</button>
</div>
