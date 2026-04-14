@extends('layouts.guest')

@section('title', 'Entorno de la propiedad')

@section('style')
    <link href="{{ asset('css/environment.css') }}" rel="stylesheet">
@endsection

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <section class="page-hero page-hero-lg">
        <div class="page-hero-media" style="background: url('/images/images.jpg') no-repeat center center / cover;"></div>
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="page-hero-copy">
                <h1 class="fw-light">Conoce el entorno</h1>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <p class="text-center mb-5">
            La Costa Blanca Norte, desde Denia hasta Altea, es conocida como una de las zonas mas hermosas del mundo para
            vivir, disfrutar y descansar. Cada zona es unica y ofrece una experiencia especial para aquellos que buscan
            calidad de vida en un entorno incomparable.
        </p>
    </section>

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

    <section class="container py-5">
        <h2 class="text-center mb-4">Mapa interactivo</h2>
        <div id="map" style="height: 400px;"></div>
    </section>

@endsection
