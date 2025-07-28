@extends('layouts.guest')

@section('title', 'Entorno de la propiedad')

@section('style')
    <link href="{{ asset(path: 'css/environment.css') }}" rel="stylesheet">
@endsection

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <section class="hero-image"
        style="background: url('/images/environment-hero.jpg') no-repeat center center; background-size: cover; height: 60vh;">
        <div class="container d-flex align-items-center h-100">
            <h1 class="text-white fw-light">Conoce el entorno</h1>
        </div>
    </section>

    <section class="container py-5">
        <p class="text-center mb-5">
            La Costa Blanca Norte, desde Dénia hasta Altea, es conocida como una de las zonas más hermosas del mundo para
            vivir, disfrutar y descansar. Cada zona es única y ofrece una experiencia especial para aquellos que buscan
            calidad de vida en un entorno incomparable.
        </p>
    </section>

    {{-- Ubicaciones --}}

    <div class="row row-cols-1 row-cols-md-3 g-4 mx-4 justify-content-center">
        @foreach ($zonas as $zona)
            <div class="col">
                <div class="card position-relative border-0 shadow-sm">
                    <img src="{{ asset('storage/' . $zona->imagen_principal) }}" class="card-img-top w-100"
                        style="height: 240px; object-fit: cover;" alt="{{ $zona->nombre }}">
                    <div class="card-img-overlay d-flex justify-content-center align-items-center">
                        <h4 class="text-white fw-semibold text-center"
                            style="background-color: rgba(29, 29, 31, 0.65); padding: 12px 24px; border-radius: 12px; font-size: 1.25rem;">
                            {{ $zona->nombre }}
                        </h4>
                    </div>
                    <a href="{{ route('zonas.show', ['slug' => $zona->slug]) }}" class="stretched-link"></a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Mapa interactivo --}}
    <section class="container py-5">
        <h2 class="text-center mb-4">Mapa interactivo</h2>
        <div id="map" style="height: 400px;"></div> <!-- Mapa se insertará aquí -->
    </section>

@endsection
