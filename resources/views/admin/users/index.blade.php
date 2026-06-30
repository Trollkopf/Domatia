@extends('layouts.admin')

@section('title', 'Usuarios')

@section('styles')
    <style>
        .team-metric-card,
        .group-card {
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .team-metric-card {
            padding: 1.2rem;
        }

        .team-metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .group-card {
            padding: 1.25rem;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="mb-1">Usuarios y grupos</h1>
            <p class="text-muted mb-0">Crea cuentas internas, organiza el equipo por grupos y controla quien entra al backoffice y a que areas puede acceder.</p>
        </div>

        <a href="{{ route('admin.users.create') }}" class="btn btn-main">Nuevo usuario</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="team-metric-card">
                <div class="text-muted small mb-2">Usuarios totales</div>
                <div class="team-metric-value">{{ $stats['users_total'] }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="team-metric-card">
                <div class="text-muted small mb-2">Con acceso al backoffice</div>
                <div class="team-metric-value">{{ $stats['backoffice_users'] }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="team-metric-card">
                <div class="text-muted small mb-2">Con gestión de usuarios</div>
                <div class="team-metric-value">{{ $stats['management_users'] }}</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle m-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Grupo</th>
                            <th>Acceso</th>
                            <th>Alta</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    @if ((int) $user->id === (int) auth()->id())
                                        <div class="small text-muted">Tu cuenta actual</div>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge rounded-pill text-bg-light border">{{ $user->groupLabel() }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge {{ $user->canAccessBackoffice() ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                            {{ $user->canAccessBackoffice() ? 'Backoffice' : 'Web pública' }}
                                        </span>
                                        @if ($user->canManageProperties())
                                            <span class="badge text-bg-primary">Propiedades</span>
                                        @endif
                                        @if ($user->canPublishProperties())
                                            <span class="badge text-bg-dark">Publicación</span>
                                        @endif
                                        @if ($user->canManageContacts())
                                            <span class="badge text-bg-success">Contactos</span>
                                        @endif
                                        @if ($user->canManageZonas())
                                            <span class="badge text-bg-success">Zonas</span>
                                        @endif
                                        @if ($user->canViewReports())
                                            <span class="badge text-bg-secondary">Informes</span>
                                        @endif
                                        @if ($user->canExportReports())
                                            <span class="badge text-bg-dark">Exporta informes</span>
                                        @endif
                                        @if ($user->canManageUsers())
                                            <span class="badge text-bg-warning">Gestión de usuarios</span>
                                        @endif
                                        @if ($user->canManageSettings())
                                            <span class="badge text-bg-info">Gestión de ajustes</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ optional($user->created_at)->format('d/m/Y') ?: 'Sin fecha' }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-dark">Editar</a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Seguro que quieres eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" {{ (int) $user->id === (int) auth()->id() ? 'disabled' : '' }}>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mb-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-xl-4">
            <div class="group-card">
                <h2 class="h5 mb-2">Nuevo grupo</h2>
                <p class="text-muted small mb-3">Crea perfiles de trabajo para el equipo: administradores, comerciales, moderadores o los que necesites.</p>

                <form action="{{ route('admin.users.groups.store') }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label class="form-label">Nombre del grupo</label>
                        <input type="text" name="group_name" class="form-control @error('group_name') is-invalid @enderror" value="{{ old('group_name') }}" required>
                        @error('group_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="group_description" class="form-control @error('group_description') is-invalid @enderror" rows="3">{{ old('group_description') }}</textarea>
                        @error('group_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="small text-muted mb-3">Cualquier permiso de area activa automaticamente el acceso al backoffice para ese grupo.</div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="group_can_access_backoffice" value="1" id="group_can_access_backoffice" @checked(old('group_can_access_backoffice'))>
                            <label class="form-check-label" for="group_can_access_backoffice">Puede entrar al backoffice</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_manage_users" value="1" id="group_can_manage_users" @checked(old('group_can_manage_users'))>
                            <label class="form-check-label" for="group_can_manage_users">Puede gestionar usuarios y grupos</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_manage_settings" value="1" id="group_can_manage_settings" @checked(old('group_can_manage_settings'))>
                            <label class="form-check-label" for="group_can_manage_settings">Puede gestionar ajustes globales</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_manage_properties" value="1" id="group_can_manage_properties" @checked(old('group_can_manage_properties'))>
                            <label class="form-check-label" for="group_can_manage_properties">Puede gestionar propiedades</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_publish_properties" value="1" id="group_can_publish_properties" @checked(old('group_can_publish_properties'))>
                            <label class="form-check-label" for="group_can_publish_properties">Puede publicar y destacar propiedades</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_manage_contacts" value="1" id="group_can_manage_contacts" @checked(old('group_can_manage_contacts'))>
                            <label class="form-check-label" for="group_can_manage_contacts">Puede gestionar contactos</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_manage_zonas" value="1" id="group_can_manage_zonas" @checked(old('group_can_manage_zonas'))>
                            <label class="form-check-label" for="group_can_manage_zonas">Puede gestionar zonas</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_view_reports" value="1" id="group_can_view_reports" @checked(old('group_can_view_reports'))>
                            <label class="form-check-label" for="group_can_view_reports">Puede acceder a informes</label>
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="group_can_export_reports" value="1" id="group_can_export_reports" @checked(old('group_can_export_reports'))>
                            <label class="form-check-label" for="group_can_export_reports">Puede exportar informes</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-main w-100">Crear grupo</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="d-grid gap-3">
                @foreach ($groups as $group)
                    <div class="group-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h2 class="h5 mb-0">{{ $group->name }}</h2>
                                    <span class="badge rounded-pill text-bg-light border">{{ $group->users_count }} usuarios</span>
                                </div>
                                <div class="small text-muted mt-1">Slug interno: {{ $group->slug }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <span class="badge {{ $group->can_access_backoffice ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                        {{ $group->can_access_backoffice ? 'Backoffice' : 'Solo web' }}
                                    </span>
                                    @if ($group->can_manage_properties)
                                        <span class="badge text-bg-primary">Propiedades</span>
                                    @endif
                                    @if ($group->can_publish_properties)
                                        <span class="badge text-bg-dark">Publicación</span>
                                    @endif
                                    @if ($group->can_manage_contacts)
                                        <span class="badge text-bg-success">Contactos</span>
                                    @endif
                                    @if ($group->can_manage_zonas)
                                        <span class="badge text-bg-success">Zonas</span>
                                    @endif
                                    @if ($group->can_view_reports)
                                        <span class="badge text-bg-secondary">Informes</span>
                                    @endif
                                    @if ($group->can_export_reports)
                                        <span class="badge text-bg-dark">Exporta informes</span>
                                    @endif
                                    @if ($group->can_manage_users)
                                        <span class="badge text-bg-warning">Gestión de usuarios</span>
                                    @endif
                                    @if ($group->can_manage_settings)
                                        <span class="badge text-bg-info">Gestión de ajustes</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.users.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Seguro que quieres eliminar este grupo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" {{ $group->users_count > 0 ? 'disabled' : '' }}>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form action="{{ route('admin.users.groups.update', $group) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre visible</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $group->name) }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Descripción</label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description', $group->description) }}">
                                </div>

                                <div class="col-12">
                                    <div class="d-flex flex-wrap gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_access_backoffice" value="1" id="group-{{ $group->id }}-backoffice" @checked($group->can_access_backoffice)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-backoffice">Acceso al backoffice</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_users" value="1" id="group-{{ $group->id }}-users" @checked($group->can_manage_users)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-users">Gestión de usuarios</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_settings" value="1" id="group-{{ $group->id }}-settings" @checked($group->can_manage_settings)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-settings">Gestión de ajustes</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_properties" value="1" id="group-{{ $group->id }}-properties" @checked($group->can_manage_properties)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-properties">Propiedades</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_publish_properties" value="1" id="group-{{ $group->id }}-publish" @checked($group->can_publish_properties)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-publish">Publicación</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_contacts" value="1" id="group-{{ $group->id }}-contacts" @checked($group->can_manage_contacts)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-contacts">Contactos</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_manage_zonas" value="1" id="group-{{ $group->id }}-zonas" @checked($group->can_manage_zonas)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-zonas">Zonas</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_view_reports" value="1" id="group-{{ $group->id }}-reports" @checked($group->can_view_reports)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-reports">Informes</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="can_export_reports" value="1" id="group-{{ $group->id }}-reports-export" @checked($group->can_export_reports)>
                                            <label class="form-check-label" for="group-{{ $group->id }}-reports-export">Exportar informes</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-outline-dark btn-sm">Guardar grupo</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
