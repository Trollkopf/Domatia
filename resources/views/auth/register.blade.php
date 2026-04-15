@extends('layouts.guest')

@section('title', __('ui.auth.register_title'))

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
                        <h3 class="text-center mb-4">{{ __('ui.auth.register_heading') }}</h3>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('ui.auth.full_name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('ui.auth.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('ui.auth.password') }}</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('ui.auth.confirm_password') }}</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            @if(Auth::user() && Auth::user()->role == 'admin')
                                <div class="mb-3">
                                    <label for="role" class="form-label">{{ __('ui.auth.role') }}</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="user">{{ __('ui.auth.user') }}</option>
                                        <option value="admin">{{ __('ui.auth.admin') }}</option>
                                    </select>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-main w-100">{{ __('ui.auth.create_account') }}</button>
                        </form>

                        <div class="text-center mt-4">
                            <p>{{ __('ui.auth.already_have_account') }} <a href="{{ route('login') }}" class="text-muted">{{ __('ui.auth.login') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
