@extends('layouts.admin')

@section('title', 'Propietarios')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Propietarios</h1>
            <p class="text-muted mb-0">Gestiona propietarios, datos de contacto y propiedades vinculadas.</p>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-dark">Ver propiedades</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-xl-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-1">Nuevo propietario</h2>
                    <p class="text-muted small mb-4">Podrás asignarlo después desde cualquier ficha de propiedad.</p>

                    <form action="{{ route('admin.propietarios.store') }}" method="POST">
                        @include('admin.propietarios._form')

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-main">Guardar propietario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.propietarios.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label for="owner-search" class="form-label">Buscar propietario</label>
                            <input type="search" id="owner-search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre, teléfono o email">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-main flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.propietarios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Propietario</th>
                                <th>Contacto</th>
                                <th class="text-center">Propiedades</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($propietarios as $propietario)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $propietario->nombre }}</div>
                                        @if ($propietario->notas)
                                            <div class="small text-muted text-truncate" style="max-width:260px;">{{ $propietario->notas }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($propietario->telefono)
                                            <div><a href="tel:{{ $propietario->telefono }}" class="text-decoration-none">{{ $propietario->telefono }}</a></div>
                                        @endif
                                        @if ($propietario->email)
                                            <div class="small"><a href="mailto:{{ $propietario->email }}" class="text-decoration-none">{{ $propietario->email }}</a></div>
                                        @endif
                                        @if (! $propietario->telefono && ! $propietario->email)
                                            <span class="text-muted">Sin datos</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="badge bg-secondary">{{ $propietario->properties_count }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.propietarios.edit', $propietario) }}" class="btn btn-sm btn-outline-dark">Abrir ficha</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-5">No hay propietarios con esos criterios.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $propietarios->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
