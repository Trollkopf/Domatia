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
                    <div class="col-md-6">
                        <label class="form-label">Nombre EN</label>
                        <input type="text" name="nombre_en" class="form-control" value="{{ $zona->nombre_en }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nombre FR</label>
                        <input type="text" name="nombre_fr" class="form-control" value="{{ $zona->nombre_fr }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nombre DE</label>
                        <input type="text" name="nombre_de" class="form-control" value="{{ $zona->nombre_de }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nombre RU</label>
                        <input type="text" name="nombre_ru" class="form-control" value="{{ $zona->nombre_ru }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagen principal</label>
                    @if ($zona->imagen_principal)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $zona->imagen_principal) }}" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    @endif
                    <input type="file" name="imagen_principal" class="form-control">
                </div>

                <hr>
                <h6 class="fw-semibold">Secciones</h6>
                <div id="secciones-container-{{ $zona->id }}">
                    @foreach ($zona->secciones ?? [] as $index => $seccion)
                        <div class="zona-seccion border rounded p-3 mb-3 bg-light">
                            <input type="hidden" name="secciones[{{ $index }}][id]" value="{{ $seccion->id }}">
                            <div class="mb-2">
                                <label class="form-label">Titulo base (ES)</label>
                                <input type="text" name="secciones[{{ $index }}][titulo]" class="form-control" value="{{ $seccion->titulo }}">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Titulo EN</label>
                                    <input type="text" name="secciones[{{ $index }}][titulo_en]" class="form-control" value="{{ $seccion->titulo_en }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Titulo FR</label>
                                    <input type="text" name="secciones[{{ $index }}][titulo_fr]" class="form-control" value="{{ $seccion->titulo_fr }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Titulo DE</label>
                                    <input type="text" name="secciones[{{ $index }}][titulo_de]" class="form-control" value="{{ $seccion->titulo_de }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Titulo RU</label>
                                    <input type="text" name="secciones[{{ $index }}][titulo_ru]" class="form-control" value="{{ $seccion->titulo_ru }}">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Imagen</label><br>
                                @if ($seccion->imagen)
                                    <img src="{{ asset('storage/' . $seccion->imagen) }}" class="img-thumbnail mb-2" style="max-width: 150px;">
                                @endif
                                <input type="file" name="secciones[{{ $index }}][imagen]" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Descripcion base (ES)</label>
                                <textarea name="secciones[{{ $index }}][descripcion]" class="form-control">{{ $seccion->descripcion }}</textarea>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Descripcion EN</label>
                                    <textarea name="secciones[{{ $index }}][descripcion_en]" class="form-control">{{ $seccion->descripcion_en }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Descripcion FR</label>
                                    <textarea name="secciones[{{ $index }}][descripcion_fr]" class="form-control">{{ $seccion->descripcion_fr }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Descripcion DE</label>
                                    <textarea name="secciones[{{ $index }}][descripcion_de]" class="form-control">{{ $seccion->descripcion_de }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Descripcion RU</label>
                                    <textarea name="secciones[{{ $index }}][descripcion_ru]" class="form-control">{{ $seccion->descripcion_ru }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addSeccion({{ $zona->id }})">+ Anadir seccion</button>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-main">Guardar</button>
            </div>
        </form>
    </div>
</div>
