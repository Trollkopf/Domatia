@php
    $isEdit = isset($user) && $user->exists;
    $selectedGroupId = old('user_group_id', $user->user_group_id ?? $groups->first()?->id);
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Grupo</label>
                        <select name="user_group_id" class="form-select @error('user_group_id') is-invalid @enderror" required>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" @selected((string) $selectedGroupId === (string) $group->id)>
                                    {{ $group->name }}
                                    {{ $group->can_access_backoffice ? '- Backoffice' : '- Web publica' }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ $isEdit ? 'Nueva contrasena' : 'Contrasena' }}</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $isEdit ? '' : 'required' }}>
                        <div class="form-text">{{ $isEdit ? 'Solo rellena este campo si quieres cambiar la contrasena actual.' : 'Se solicitara al usuario en su primer acceso.' }}</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ $isEdit ? 'Confirmar nueva contrasena' : 'Confirmar contrasena' }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <h2 class="h5 mb-2">Resumen de grupos</h2>
                <p class="text-muted small mb-3">Cada usuario hereda el acceso y las capacidades del grupo asignado.</p>

                <div class="d-grid gap-3">
                    @foreach ($groups as $group)
                        <div class="border rounded-4 p-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">{{ $group->name }}</div>
                                    @if ($group->description)
                                        <div class="text-muted small mt-1">{{ $group->description }}</div>
                                    @endif
                                </div>

                                @if ((string) $selectedGroupId === (string) $group->id)
                                    <span class="badge text-bg-dark">Seleccionado</span>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <span class="badge {{ $group->can_access_backoffice ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                    {{ $group->can_access_backoffice ? 'Backoffice' : 'Solo web' }}
                                </span>
                                @if ($group->can_manage_properties)
                                    <span class="badge text-bg-primary">Propiedades</span>
                                @endif
                                @if ($group->can_publish_properties)
                                    <span class="badge text-bg-dark">Publicacion</span>
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
                                    <span class="badge text-bg-warning">Gestion usuarios</span>
                                @endif
                                @if ($group->can_manage_settings)
                                    <span class="badge text-bg-info">Gestion ajustes</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-end mt-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-main">{{ $isEdit ? 'Guardar cambios' : 'Crear usuario' }}</button>
</div>
