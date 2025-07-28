@extends('layouts.guest')

@section('title', $zona->nombre)

@section('content')
    <!-- Hero con imagen principal -->
    <section class="hero-image position-relative" style="height: 400px; overflow: hidden;">
        <img src="{{ asset('storage/' . $zona->imagen_principal) }}" alt="{{ $zona->nombre }}" class="w-100 h-100"
            style="object-fit: cover;">
        <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
            <h1 class="display-4 fw-semibold text-shadow bg-dark bg-opacity-50 px-4 py-2 rounded">
                {{ $zona->nombre }}
            </h1>
        </div>
    </section>

    <!-- Secciones dentro de la zona -->
    @php
        $bgClasses = ['bg-light', 'bg-white'];
    @endphp
    <section class="container py-5">
        @foreach ($zona->secciones as $index => $seccion)
            <div class="row align-items-center mb-5 py-4 px-3 rounded {{ $bgClasses[$index % count($bgClasses)] }} {{ $index % 2 === 0 ? '' : 'flex-row-reverse' }}"
                data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="col-md-6">
                    <img src="{{ asset('storage/' . $seccion->imagen) }}" alt="{{ $seccion->titulo }}"
                        class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <h3 class="fw-semibold">{{ $seccion->titulo }}</h3>
                    <p class="text-muted" style="white-space: pre-line;">
                        {{ $seccion->descripcion }}
                    </p>
                </div>
            </div>
        @endforeach
    </section>

    <!-- Propiedades dentro de la zona -->
    @if ($zona->properties->count())
        <section class="container py-5">
            <h2 class="mb-4">Propiedades en {{ $zona->nombre }}</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($zona->properties as $property)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ asset('storage/' . $property->thumbnail) }}" class="card-img-top"
                                alt="{{ $property->title }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $property->title }}</h5>
                                <p class="card-text">{{ Str::limit($property->description, 100) }}</p>
                                <a href="{{ route('guest.property.show', $property->slug) }}" class="btn btn-dark">Ver más</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
