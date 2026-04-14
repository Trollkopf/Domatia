@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h1 class="m-0 fs-4">Editar usuario</h1>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select name="role" class="form-select" required>
                        <option value="user" @selected(old('role', $user->role) === 'user')>user</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>admin</option>
                    </select>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-main">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
