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
    $extraDescriptionLocales = [
        'nl' => 'Holandés',
        'pl' => 'Polaco',
        'sv' => 'Sueco',
        'da' => 'Danés',
    ];
    $featureTextareaValue = old('features_text', isset($property) ? implode("\n", $property->featuresList()) : '');
@endphp

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
                    <label class="form-label">Propietario</label>
                    <select name="propietario_id" class="form-select">
                        <option value="">-- Selecciona un propietario --</option>
                        @foreach ($propietarios as $p)
                            <option value="{{ $p->id }}" @selected(old('propietario_id', $property->propietario_id ?? '') == $p->id)>{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción (ES)</label>
                    <textarea name="description" class="form-control" rows="6">{{ old('description', $property->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

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

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">Títulos y ubicaciones por idioma</h5>
            <div class="row g-3">
                @foreach (['en' => 'EN', 'fr' => 'FR', 'de' => 'DE', 'ru' => 'RU'] as $locale => $label)
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
                @foreach (['en' => 'EN', 'fr' => 'FR', 'de' => 'DE', 'ru' => 'RU'] as $locale => $label)
                    <div class="col-md-6">
                        <label class="form-label">Descripción {{ $label }}</label>
                        <textarea name="description_{{ $locale }}" class="form-control" rows="6">{{ old('description_' . $locale, $property->{'description_' . $locale} ?? '') }}</textarea>
                    </div>
                @endforeach

                @foreach ($extraDescriptionLocales as $locale => $label)
                    <div class="col-md-6">
                        <label class="form-label">Descripción {{ $label }}</label>
                        <textarea name="description_extra[{{ $locale }}]" class="form-control" rows="6">{{ old('description_extra.' . $locale, $property->description_extra[$locale] ?? '') }}</textarea>
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
                @foreach (['' => 'ES', '_en' => 'EN', '_fr' => 'FR', '_de' => 'DE', '_ru' => 'RU'] as $suffix => $label)
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

    @if (!isset($property))
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <label class="form-label">Imágenes de la propiedad</label>
                <div class="dropzone" id="dropzone">
                    Arrastra las imágenes aquí o haz clic para seleccionar
                    <input type="file" name="images[]" id="images" class="form-control d-none" multiple>
                </div>
                <div class="form-text mt-2">La primera imagen se usará como miniatura principal.</div>
            </div>
        </div>
    @endif

    <div class="text-end">
        <button type="submit" class="btn btn-main">Guardar</button>
    </div>
</div>
