@extends('layouts.admin')

@section('title', 'Editar usuario')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Editar usuario</h1>
            <p class="text-muted mb-0">Ajusta los datos de acceso, la contraseña y el grupo operativo del usuario.</p>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        @include('admin.users._form', ['user' => $user, 'groups' => $groups])
    </form>
@endsection
