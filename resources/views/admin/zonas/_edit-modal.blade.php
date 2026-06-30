<div class="modal fade" id="editZonaModal{{ $zona->id }}" tabindex="1" aria-labelledby="editModalLabel{{ $zona->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.zonas.update', $zona) }}" method="POST" enctype="multipart/form-data" class="modal-content zona-edit-form" data-id="{{ $zona->id }}">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $zona->id }}">Editar Zona</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre base (ES)</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $zona->nombre }}" required>
                </div>

                <div class="row g-3 mb-3">
                    @foreach ($zonaTranslationLocales as $locale => $label)
                        <div class="col-md-6">
                            <label class="form-label">Nombre {{ $label }}</label>
                            <input type="text" name="nombre_{{ $locale }}" class="form-control" value="{{ $zona->{'nombre_' . $locale} }}">
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagen principal</label>
                    <div class="mb-2">
                        <img src="{{ $zona->imageUrl() }}" alt="Imagen de {{ $zona->nombre }}" class="img-thumbnail" style="width: 200px; height: 110px; object-fit: cover;">
                        @if (! $zona->hasCustomImage())
                            <div class="form-text">
                                {{ $zona->usesPropertyImage() ? 'Se está utilizando automáticamente una foto de una propiedad de esta zona.' : 'No hay fotos disponibles; se está utilizando la imagen global.' }}
                            </div>
                        @endif
                    </div>
                    <input type="file" name="imagen_principal" class="form-control">
                    <div class="form-text">Formatos admitidos: JPG, PNG, WEBP. Tamaño recomendado: hasta 10 MB.</div>
                </div>

                <hr>
                <h6 class="fw-semibold">Secciones</h6>
                <div id="secciones-container-{{ $zona->id }}">
                    @foreach ($zona->secciones ?? [] as $index => $seccion)
                        <div class="zona-seccion border rounded p-3 mb-3 bg-light">
                            <input type="hidden" name="secciones[{{ $index }}][id]" value="{{ $seccion->id }}">
                            <div class="mb-2">
                                <label class="form-label">Título base (ES)</label>
                                <input type="text" name="secciones[{{ $index }}][titulo]" class="form-control" value="{{ $seccion->titulo }}">
                            </div>
                            <div class="row g-2 mb-2">
                                @foreach ($zonaTranslationLocales as $locale => $label)
                                    <div class="col-md-6">
                                        <label class="form-label">Título {{ $label }}</label>
                                        <input type="text" name="secciones[{{ $index }}][titulo_{{ $locale }}]" class="form-control" value="{{ $seccion->{'titulo_' . $locale} }}">
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Imagen</label><br>
                                @if ($seccion->imagen)
                                    <img src="{{ asset('storage/' . $seccion->imagen) }}" class="img-thumbnail mb-2" style="max-width: 150px;">
                                @endif
                                <input type="file" name="secciones[{{ $index }}][imagen]" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Descripción base (ES)</label>
                                <textarea name="secciones[{{ $index }}][descripcion]" class="form-control">{{ $seccion->descripcion }}</textarea>
                            </div>
                            <div class="row g-2">
                                @foreach ($zonaTranslationLocales as $locale => $label)
                                    <div class="col-md-6">
                                        <label class="form-label">Descripción {{ $label }}</label>
                                        <textarea name="secciones[{{ $index }}][descripcion_{{ $locale }}]" class="form-control">{{ $seccion->{'descripcion_' . $locale} }}</textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSeccion({{ $zona->id }})">+ Añadir sección</button>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-main">Guardar</button>
            </div>
        </form>
    </div>
</div>
