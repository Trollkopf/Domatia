@extends('layouts.admin')

@section('title', 'Gestión de zonas')

@php($zonaTranslationLocales = collect(config('app.supported_locales'))->except(config('app.locale'))->all())

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Zonas</h1>
        <button class="btn btn-main" data-bs-toggle="modal" data-bs-target="#createZonaModal">
            + Nueva Zona
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">No se pudieron guardar las imágenes o los datos de la zona.</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped m-0" id="zona-table">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="zona-tbody">
                    @foreach ($zonas as $zona)
                        @include('admin.zonas._row', ['zona' => $zona])
                    @endforeach
                </tbody>
            </table>

            @foreach ($zonas as $zona)
                @include('admin.zonas._edit-modal', ['zona' => $zona])
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="createZonaModal" tabindex="-1" aria-labelledby="createZonaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.zonas.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createZonaModalLabel">Nueva Zona</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre base (ES)</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="row g-3 mb-3">
                        @foreach ($zonaTranslationLocales as $locale => $label)
                            <div class="col-md-6">
                                <label class="form-label">Nombre {{ $label }}</label>
                                <input type="text" name="nombre_{{ $locale }}" class="form-control">
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Imagen principal</label>
                        <input type="file" name="imagen_principal" class="form-control" accept="image/*">
                        <div class="form-text">Si no subes ninguna, se utilizará automáticamente una foto de las propiedades de la zona.</div>
                    </div>

                    <hr>
                    <h6 class="fw-semibold">Secciones</h6>
                    <div id="secciones-container">
                        <div class="zona-seccion border rounded p-3 mb-3 bg-light">
                            <div class="mb-2">
                                <label class="form-label">Título base (ES)</label>
                                <input type="text" name="secciones[0][titulo]" class="form-control">
                            </div>
                            <div class="row g-2 mb-2">
                                @foreach ($zonaTranslationLocales as $locale => $label)
                                    <div class="col-md-6">
                                        <label class="form-label">Título {{ $label }}</label>
                                        <input type="text" name="secciones[0][titulo_{{ $locale }}]" class="form-control">
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Imagen</label>
                                <input type="file" name="secciones[0][imagen]" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Descripción base (ES)</label>
                                <textarea name="secciones[0][descripcion]" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="row g-2">
                                @foreach ($zonaTranslationLocales as $locale => $label)
                                    <div class="col-md-6">
                                        <label class="form-label">Descripción {{ $label }}</label>
                                        <textarea name="secciones[0][descripcion_{{ $locale }}]" class="form-control" rows="2"></textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="add-seccion">+ Añadir sección</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-main">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    let seccionIndex = 1;

    document.getElementById('add-seccion').addEventListener('click', () => {
    const container = document.getElementById('secciones-container');
    const html = `
    <div class="zona-seccion border rounded p-3 mb-3 bg-light">
        <div class="mb-2">
            <label class="form-label">Título base (ES)</label>
            <input type="text" name="secciones[${seccionIndex}][titulo]" class="form-control">
        </div>
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <label class="form-label">Título EN</label>
                <input type="text" name="secciones[${seccionIndex}][titulo_en]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Título FR</label>
                <input type="text" name="secciones[${seccionIndex}][titulo_fr]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Título DE</label>
                <input type="text" name="secciones[${seccionIndex}][titulo_de]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Título RU</label>
                <input type="text" name="secciones[${seccionIndex}][titulo_ru]" class="form-control">
            </div>
            @foreach (['nl' => 'Nederlands', 'pl' => 'Polski', 'sv' => 'Svenska', 'da' => 'Dansk'] as $locale => $label)
                <div class="col-md-6">
                    <label class="form-label">Título {{ $label }}</label>
                    <input type="text" name="secciones[${seccionIndex}][titulo_{{ $locale }}]" class="form-control">
                </div>
            @endforeach
        </div>
        <div class="mb-2">
            <label class="form-label">Imagen</label>
            <input type="file" name="secciones[${seccionIndex}][imagen]" class="form-control" accept="image/*">
        </div>
        <div class="mb-2">
            <label class="form-label">Descripción base (ES)</label>
            <textarea name="secciones[${seccionIndex}][descripcion]" class="form-control" rows="2"></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Descripción EN</label>
                <textarea name="secciones[${seccionIndex}][descripcion_en]" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descripción FR</label>
                <textarea name="secciones[${seccionIndex}][descripcion_fr]" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descripción DE</label>
                <textarea name="secciones[${seccionIndex}][descripcion_de]" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descripción RU</label>
                <textarea name="secciones[${seccionIndex}][descripcion_ru]" class="form-control" rows="2"></textarea>
            </div>
            @foreach (['nl' => 'Nederlands', 'pl' => 'Polski', 'sv' => 'Svenska', 'da' => 'Dansk'] as $locale => $label)
                <div class="col-md-6">
                    <label class="form-label">Descripción {{ $label }}</label>
                    <textarea name="secciones[${seccionIndex}][descripcion_{{ $locale }}]" class="form-control" rows="2"></textarea>
                </div>
            @endforeach
        </div>
    </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    seccionIndex++;
    });
    });

    function addSeccion(zonaId) {
    const container = document.getElementById('secciones-container-' + zonaId);
    const index = container.querySelectorAll('.zona-seccion').length;

    const sectionHTML = `
    <div class="zona-seccion border rounded p-3 mb-3 bg-light">
        <div class="mb-2">
            <label class="form-label">Título base (ES)</label>
            <input type="text" name="secciones[${index}][titulo]" class="form-control">
        </div>
        <div class="row g-2 mb-2">
            <div class="col-md-6">
                <label class="form-label">Título EN</label>
                <input type="text" name="secciones[${index}][titulo_en]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Título FR</label>
                <input type="text" name="secciones[${index}][titulo_fr]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Título DE</label>
                <input type="text" name="secciones[${index}][titulo_de]" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Título RU</label>
                <input type="text" name="secciones[${index}][titulo_ru]" class="form-control">
            </div>
            @foreach (['nl' => 'Nederlands', 'pl' => 'Polski', 'sv' => 'Svenska', 'da' => 'Dansk'] as $locale => $label)
                <div class="col-md-6">
                    <label class="form-label">Título {{ $label }}</label>
                    <input type="text" name="secciones[${index}][titulo_{{ $locale }}]" class="form-control">
                </div>
            @endforeach
        </div>
        <div class="mb-2">
            <label class="form-label">Imagen</label>
            <input type="file" name="secciones[${index}][imagen]" class="form-control">
        </div>
        <div class="mb-2">
            <label class="form-label">Descripción base (ES)</label>
            <textarea name="secciones[${index}][descripcion]" class="form-control"></textarea>
        </div>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Descripción EN</label>
                <textarea name="secciones[${index}][descripcion_en]" class="form-control"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descripción FR</label>
                <textarea name="secciones[${index}][descripcion_fr]" class="form-control"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descripción DE</label>
                <textarea name="secciones[${index}][descripcion_de]" class="form-control"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Descripción RU</label>
                <textarea name="secciones[${index}][descripcion_ru]" class="form-control"></textarea>
            </div>
            @foreach (['nl' => 'Nederlands', 'pl' => 'Polski', 'sv' => 'Svenska', 'da' => 'Dansk'] as $locale => $label)
                <div class="col-md-6">
                    <label class="form-label">Descripción {{ $label }}</label>
                    <textarea name="secciones[${index}][descripcion_{{ $locale }}]" class="form-control"></textarea>
                </div>
            @endforeach
        </div>
    </div>
    `;

    container.insertAdjacentHTML('beforeend', sectionHTML);
    }

    </script>
@endpush
