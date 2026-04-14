@extends('layouts.guest')

@section('title', 'Conocenos')

@section('style')
    <link href="{{ asset('css/about.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="page-hero page-hero-lg">
        <div class="page-hero-media" style="background: url('/images/our-company.jpg') no-repeat center center / cover;"></div>
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="page-hero-copy">
                <h1 class="fw-light">{{ $siteSettings['company_name'] }}</h1>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <h2 class="text-center mb-4">{{ $siteSettings['about_heading'] }}</h2>
        <p class="lead">
            {{ $siteSettings['about_body'] }}
        </p>
    </section>

    <section class="container py-5">
        <h2 class="text-center mb-4">Contacta con nosotros</h2>

        <form action="{{ route('contact.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label">Tu nombre *</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Ingresa tu nombre" required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Tu email *</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="col-12">
                <label for="message" class="form-label">Tu mensaje *</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Escribe tu mensaje" required></textarea>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-main w-50">Enviar mensaje</button>
            </div>
        </form>
    </section>
@endsection
