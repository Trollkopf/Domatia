@extends('layouts.admin')

@section('title', 'Listado de Propiedades')

@section('styles')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Propiedades</h1>
            <p class="text-muted mb-0">Filtra y gestiona el catálogo desde un solo sitio.</p>
        </div>
        <a href="{{ route('admin.properties.create') }}" class="btn btn-main">+ Nueva Propiedad</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.properties.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Título, ubicación o referencia">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Zona</label>
                    <select name="zona_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}" @selected((string) request('zona_id') === (string) $zona->id)>{{ $zona->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Destacada</label>
                    <select name="featured" class="form-select">
                        <option value="">Todas</option>
                        <option value="1" @selected(request('featured') === '1')>Sí</option>
                        <option value="0" @selected(request('featured') === '0')>No</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Desde</label>
                    <input type="number" name="price_min" class="form-control" value="{{ request('price_min') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Hasta</label>
                    <input type="number" name="price_max" class="form-control" value="{{ request('price_max') }}">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-4 pt-2">
                        <input type="checkbox" class="form-check-input" id="missing_thumbnail" name="missing_thumbnail" value="1" @checked(request()->boolean('missing_thumbnail'))>
                        <label class="form-check-label" for="missing_thumbnail">Solo sin imagen principal</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-main w-100">Aplicar filtros</button>
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @include('admin.properties._table', ['properties' => $properties, 'statuses' => $statuses])
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $properties->links('pagination::bootstrap-5') }}
    </div>
@endsection
