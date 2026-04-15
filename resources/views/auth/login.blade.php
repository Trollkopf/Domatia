@extends('layouts.guest')

@section('title', __('ui.auth.login_title'))

@section('styles')
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section>
        <div class="container d-flex align-items-center pt-4">
            <h1 class="text-black fw-light">{{ __('ui.auth.login_title') }}</h1>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">{{ __('ui.auth.login_heading') }}</h3>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('ui.auth.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('ui.auth.password') }}</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">{{ __('ui.auth.remember_me') }}</label>
                            </div>

                            <button type="submit" class="btn btn-main w-100">{{ __('ui.auth.login_title') }}</button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('password.request') }}" class="text-muted">{{ __('ui.auth.forgot_password') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
