@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('styles')
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="page-hero page-hero-lg">
        <div class="page-hero-media" style="background: url('{{ $siteSettings['register_header_image'] }}') no-repeat center center / cover;"></div>
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="page-hero-copy">
                <h1 class="fw-light">{{ $siteSettings['register_header_title'] }}</h1>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Registrate</h3>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electronico</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contrasena</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar contrasena</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            @if(Auth::user() && Auth::user()->role == 'admin')
                                <div class="mb-3">
                                    <label for="role" class="form-label">Rol</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="user">Usuario</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-main w-100">Crear cuenta</button>
                        </form>

                        <div class="text-center mt-4">
                            <p>Ya tienes una cuenta? <a href="{{ route('login') }}" class="text-muted">Iniciar sesion</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
