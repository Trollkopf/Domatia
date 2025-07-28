@extends('layouts.guest')

@section('title', 'Conócenos')

@section('style')
    <link href="{{ asset(path: 'css/about.css') }}" rel="stylesheet">
@endsection

@section('content')
    <section class="hero-image"
        style="background: url('/images/our-company.jpg') no-repeat center center; background-size: cover; height: 60vh;">
        <div class="container d-flex align-items-center h-100">
            <h1 class="text-white fw-light">Conócenos </h1>
        </div>
    </section>

    <section class="container py-5">
        <h2 class="text-center mb-4">Nuestra filosofía</h2>
        <p class="lead">
            En [Tu empresa], nos dedicamos a proporcionar propiedades exclusivas con un enfoque personalizado para cada
            cliente. Creemos que cada propiedad debe ser un reflejo de los deseos y necesidades del cliente. Nuestra misión
            es ofrecer un servicio único, basado en confianza y experiencia.
        </p>

        <h3 class="mt-5 mb-4">Nuestro equipo</h3>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <img src="/images/team-member-1.jpg" class="rounded-circle mb-3"
                    style="width: 150px; height: 150px; object-fit: cover;" alt="Miembro del equipo">
                <h5>Juan Pérez</h5>
                <p>CEO</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <img src="/images/team-member-2.jpg" class="rounded-circle mb-3"
                    style="width: 150px; height: 150px; object-fit: cover;" alt="Miembro del equipo">
                <h5>Maria López</h5>
                <p>Director Comercial</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <img src="/images/team-member-3.jpg" class="rounded-circle mb-3"
                    style="width: 150px; height: 150px; object-fit: cover;" alt="Miembro del equipo">
                <h5>Carlos García</h5>
                <p>Asesor Inmobiliario</p>
            </div>
        </div>
    </section>

    {{-- TESTIMONIOS --}}
    <section class="container py-5">
        <h2 class="text-center mb-4">Testimonios</h2>

        {{-- TESTIMONIOS --}}
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial text-center">
                    <img src="/images/client-1.jpg" class="rounded-circle mb-3"
                        style="width: 100px; height: 100px; object-fit: cover;" alt="Cliente 1">
                    <p class="lead">"Excelente servicio y una atención personalizada excepcional. Nos ayudaron a
                        encontrar nuestra casa ideal. ¡Recomendados al 100%!"</p>
                    <h5>- Laura Martínez</h5>
                    <p class="text-muted">Cliente Satisfecha</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial text-center">
                    <img src="/images/client-2.jpg" class="rounded-circle mb-3"
                        style="width: 100px; height: 100px; object-fit: cover;" alt="Cliente 2">
                    <p class="lead">"Trabajo impecable. El equipo es muy profesional y entendió nuestras necesidades
                        rápidamente. ¡Gracias por todo!"</p>
                    <h5>- Andrés Gómez</h5>
                    <p class="text-muted">Inversor Inmobiliario</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial text-center">
                    <img src="/images/client-3.jpg" class="rounded-circle mb-3"
                        style="width: 100px; height: 100px; object-fit: cover;" alt="Cliente 3">
                    <p class="lead">"Nos ayudaron a vender nuestra propiedad en tiempo récord. Todo fue muy fácil y
                        fluido. Sin duda, volveremos a contar con ellos."</p>
                    <h5>- Javier Rodríguez</h5>
                    <p class="text-muted">Vendedor de Propiedad</p>
                </div>
            </div>
        </div>
    </section>

    {{-- GALERIA IMAGENES --}}
    <section class="container py-5">
        <h2 class="text-center mb-4">Galería</h2>

        <div class="row">
            <div class="col-md-4 mb-4">
                <img src="/images/property-1.jpg" class="w-100 rounded" alt="Propiedad 1">
            </div>
            <div class="col-md-4 mb-4">
                <img src="/images/property-2.jpg" class="w-100 rounded" alt="Propiedad 2">
            </div>
            <div class="col-md-4 mb-4">
                <img src="/images/team.jpg" class="w-100 rounded" alt="Nuestro equipo">
            </div>
        </div>
    </section>

    {{-- VIDEO PROMOCIONAL --}}
    <section class="container py-5">
        <h2 class="text-center mb-4">Nuestro video de presentación</h2>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <iframe width="100%" height="400" src="https://www.youtube.com/embed/tU6XhtsJgDg"
                    title="Video presentación" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </section>

    {{-- FORMULARIO CONTACTO --}}
    <section class="container py-5">
        <h2 class="text-center mb-4">Contacta con nosotros</h2>

        <form action="{{ route('contact.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label">Tu nombre *</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Ingresa tu nombre"
                    required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Tu email *</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@correo.com"
                    required>
            </div>

            <div class="col-12">
                <label for="message" class="form-label">Tu mensaje *</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Escribe tu mensaje"
                    required></textarea>
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-main w-50">Enviar mensaje</button>
            </div>
        </form>
    </section>

@endsection
