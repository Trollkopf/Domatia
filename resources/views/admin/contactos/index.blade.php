@extends('layouts.admin')

@section('title', 'Contactos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Contactos</h1>
            <p class="text-muted mb-0">Consulta y organiza los leads recibidos desde la web.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.contactos.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nombre, email o teléfono">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendiente" @selected(request('status') === 'pendiente')>Pendiente</option>
                        <option value="contactado" @selected(request('status') === 'contactado')>Contactado</option>
                        <option value="cerrado" @selected(request('status') === 'cerrado')>Cerrado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Propiedad</label>
                    <select name="property_id" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}" @selected((string) request('property_id') === (string) $property->id)>
                                {{ $property->ref ? $property->ref . ' · ' : '' }}{{ $property->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Seguimiento</label>
                    <select name="follow_up" class="form-select">
                        <option value="">Todos</option>
                        <option value="due" @selected(request('follow_up') === 'due')>Vencidos o para hoy</option>
                        <option value="scheduled" @selected(request('follow_up') === 'scheduled')>Con fecha programada</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-main w-100">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle m-0">
                <thead class="table-dark">
                    <tr>
                        <th>Contacto</th>
                        <th>Propiedad</th>
                        <th>Estado</th>
                        <th>Próxima acción</th>
                        <th>Último contacto</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contactos as $contacto)
                        @php
                            $isDue = $contacto->next_action_at && $contacto->next_action_at->isPast();
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $contacto->nombre }}</div>
                                <div class="text-muted small">{{ $contacto->email }}</div>
                                @if ($contacto->telefono)
                                    <div class="text-muted small">{{ $contacto->telefono }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($contacto->property)
                                    <div>{{ $contacto->property->title }}</div>
                                    <div class="text-muted small">{{ $contacto->property->ref }}</div>
                                @else
                                    <span class="text-muted">Sin propiedad asociada</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $contacto->status === 'pendiente' ? 'warning text-dark' : ($contacto->status === 'contactado' ? 'info text-dark' : 'success') }}">
                                    {{ ucfirst($contacto->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($contacto->next_action_at)
                                    <span class="{{ $isDue ? 'text-danger fw-semibold' : '' }}">
                                        {{ $contacto->next_action_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Sin programar</span>
                                @endif
                            </td>
                            <td>
                                {{ $contacto->last_contacted_at ? $contacto->last_contacted_at->format('d/m/Y H:i') : 'Sin registrar' }}
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ route('admin.contactos.quick-update', $contacto) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="contactado">
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                        <input type="hidden" name="filter_status" value="{{ request('status') }}">
                                        <input type="hidden" name="property_id" value="{{ request('property_id') }}">
                                        <input type="hidden" name="follow_up" value="{{ request('follow_up') }}">
                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Marcar contacto</button>
                                    </form>
                                    <a href="{{ route('admin.contactos.show', $contacto) }}" class="btn btn-sm btn-outline-primary">Ver detalle</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No hay contactos con los filtros actuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $contactos->links('pagination::bootstrap-5') }}
    </div>
@endsection
