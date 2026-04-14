@extends('layouts.guest')

@section('title', $zona->nombre)

@section('content')
    <section class="page-hero" style="min-height: 400px;">
        <img src="{{ asset('storage/' . $zona->imagen_principal) }}" alt="{{ $zona->nombre }}" class="page-hero-media">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="page-hero-copy text-center mx-auto">
                <h1 class="display-4 fw-semibold">{{ $zona->nombre }}</h1>
            </div>
        </div>
    </section>

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
                                <a href="{{ route('guest.property.show', $property->slug) }}" class="btn btn-dark">Ver mas</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
