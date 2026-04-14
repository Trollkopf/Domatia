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
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="{{ $zona->nombre }}" required>
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
                                <label class="form-label">Título</label>
                                <input type="text" name="secciones[{{ $index }}][titulo]" class="form-control" value="{{ $seccion->titulo }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Imagen</label><br>
                                @if ($seccion->imagen)
                                    <img src="{{ asset('storage/' . $seccion->imagen) }}" class="img-thumbnail mb-2" style="max-width: 150px;">
                                @endif
                                <input type="file" name="secciones[{{ $index }}][imagen]" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Descripción</label>
                                <textarea name="secciones[{{ $index }}][descripcion]" class="form-control">{{ $seccion->descripcion }}</textarea>
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
