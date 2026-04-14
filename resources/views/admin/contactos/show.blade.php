@extends('layouts.admin')

@section('title', 'Detalle de Contacto')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Detalle del contacto</h1>
            <p class="text-muted mb-0">Revisa el lead y actualiza su seguimiento comercial.</p>
        </div>
        <a href="{{ route('admin.contactos.index') }}" class="btn btn-outline-secondary">Volver</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Mensaje recibido</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">Nombre</div>
                        <div class="fw-semibold">{{ $contacto->nombre }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Email</div>
                        <div>{{ $contacto->email }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Teléfono</div>
                        <div>{{ $contacto->telefono ?: 'No facilitado' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Recibido el</div>
                        <div>{{ $contacto->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small">Mensaje</div>
                        <div class="border rounded p-3 bg-light" style="white-space: pre-line;">{{ $contacto->mensaje ?: 'Sin mensaje adicional.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Seguimiento comercial</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contactos.update', $contacto) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select name="status" class="form-select">
                                <option value="pendiente" @selected($contacto->status === 'pendiente')>Pendiente</option>
                                <option value="contactado" @selected($contacto->status === 'contactado')>Contactado</option>
                                <option value="cerrado" @selected($contacto->status === 'cerrado')>Cerrado</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Próxima acción</label>
                            <input type="date" name="next_action_at" class="form-control" value="{{ old('next_action_at', optional($contacto->next_action_at)->format('Y-m-d')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas internas</label>
                            <textarea name="internal_notes" class="form-control" rows="5" placeholder="Ej. llamó interesado en visita, prefiere contacto por WhatsApp...">{{ old('internal_notes', $contacto->internal_notes) }}</textarea>
                        </div>

                        <div class="mb-3 small text-muted">
                            Último contacto: {{ $contacto->last_contacted_at ? $contacto->last_contacted_at->format('d/m/Y H:i') : 'Sin registrar' }}
                        </div>

                        <button type="submit" class="btn btn-main w-100">Guardar seguimiento</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Propiedad asociada</h5>
                </div>
                <div class="card-body">
                    @if ($contacto->property)
                        <div class="fw-semibold">{{ $contacto->property->title }}</div>
                        <div class="text-muted small mb-2">{{ $contacto->property->ref }}</div>
                        <div class="text-muted small mb-3">{{ $contacto->property->location }}</div>
                        <a href="{{ route('admin.properties.edit', $contacto->property) }}" class="btn btn-outline-primary btn-sm">
                            Abrir propiedad
                        </a>
                    @else
                        <p class="text-muted mb-0">Este contacto no está vinculado a una propiedad concreta.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
