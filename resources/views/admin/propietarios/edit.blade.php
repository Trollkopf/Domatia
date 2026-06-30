@extends('layouts.admin')

@section('title', 'Editar propietario')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <a href="{{ route('admin.propietarios.index') }}" class="small text-decoration-none">← Volver a propietarios</a>
            <h1 class="mb-1 mt-2">{{ $propietario->nombre }}</h1>
            <p class="text-muted mb-0">Datos de contacto y propiedades vinculadas.</p>
        </div>
        <form action="{{ route('admin.propietarios.destroy', $propietario) }}" method="POST" onsubmit="return confirm('¿Eliminar este propietario? Sus propiedades quedarán sin propietario asignado.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Eliminar propietario</button>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-xl-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-4">Datos del propietario</h2>
                    <form action="{{ route('admin.propietarios.update', $propietario) }}" method="POST">
                        @method('PUT')
                        @include('admin.propietarios._form')

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-main">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-body d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <h2 class="h5 mb-1">Propiedades vinculadas</h2>
                        <p class="text-muted small mb-0">{{ $properties->total() }} propiedades asignadas.</p>
                    </div>
                    <a href="{{ route('admin.properties.create') }}" class="btn btn-sm btn-main">Nueva propiedad</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Referencia</th>
                                <th>Propiedad</th>
                                <th>Estado</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($properties as $property)
                                <tr>
                                    <td>{{ $property->ref ?: '—' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $property->title }}</div>
                                        <div class="small text-muted">{{ $property->location ?: ($property->zona->nombre ?? 'Sin ubicación') }}</div>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($property->status ?: 'draft') }}</span></td>
                                    <td class="text-end"><a href="{{ route('admin.properties.edit', $property) }}" class="btn btn-sm btn-outline-dark">Editar</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-5">Este propietario todavía no tiene propiedades asignadas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-3">{{ $properties->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection
