@extends('layouts.guest')

@section('title', 'Contactanos')

@section('style')
<link href="{{ asset('css/contact.css') }}" rel="stylesheet">
@endsection

@section('content')
<section class="page-hero page-hero-lg">
    <div class="page-hero-media" style="background: url('{{ $siteSettings['contact_header_image'] }}') no-repeat center center / cover;"></div>
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content">
        <div class="page-hero-copy">
            <h1 class="fw-light">{{ $siteSettings['contact_header_title'] }}</h1>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <h2>Informacion de contacto</h2>
            <p class="text-muted">{{ $siteSettings['contact_intro'] }}</p>
            <div class="d-flex flex-column mb-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-telephone me-3" style="font-size: 1.5rem;"></i>
                    <p class="mb-0">Tel: {{ $siteSettings['company_phone'] ?: 'Pendiente de configurar' }}</p>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-envelope me-3" style="font-size: 1.5rem;"></i>
                    <p class="mb-0">Email: {{ $siteSettings['company_email'] ?: 'Pendiente de configurar' }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt me-3" style="font-size: 1.5rem;"></i>
                    <p class="mb-0">{{ $siteSettings['company_address'] ?: 'Direccion pendiente de configurar' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h2>Envianos tu mensaje</h2>
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre *</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Escribe tu nombre" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electronico *</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Escribe tu correo electronico" required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Mensaje *</label>
                    <textarea class="form-control" id="message" name="message" rows="4" placeholder="Escribe tu mensaje" required></textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="accept_terms" name="accept_terms" required>
                    <label class="form-check-label" for="accept_terms">Acepto los terminos y condiciones</label>
                </div>

                <button type="submit" class="btn btn-main w-100">Enviar mensaje</button>
            </form>
        </div>
    </div>
</section>
@endsection
