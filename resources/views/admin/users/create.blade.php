@extends('layouts.admin')

@section('title', 'Nuevo usuario')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Nuevo usuario</h1>
            <p class="text-muted mb-0">Crea cuentas internas y asignales el grupo correcto para trabajar en la app.</p>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        @include('admin.users._form', ['user' => $user, 'groups' => $groups])
    </form>
@endsection
