@extends('layouts.guest')

@section('title', $property->title)

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                @php
                    $mainImage = $property->thumbnail ?: null;
                @endphp

                <a
                    href="{{ $mainImage ? asset('storage/' . $mainImage) : asset('images/our-company.jpg') }}"
                    class="glightbox position-relative d-block overflow-hidden"
                    data-gallery="property-gallery"
                    style="aspect-ratio: 4/3;"
                >
                    <img
                        id="mainImage"
                        src="{{ $mainImage ? asset('storage/' . $mainImage) : asset('images/our-company.jpg') }}"
                        class="w-100 h-100"
                        style="object-fit: cover;"
                        alt="{{ $property->title }}"
                    >
                </a>

                <div class="row g-2 mt-2">
                    @foreach ($property->images as $index => $img)
                        <div class="col-3">
                            <a href="{{ asset('storage/' . $img->path) }}" class="glightbox" data-gallery="property-gallery">
                                <img
                                    src="{{ asset('storage/' . $img->path) }}"
                                    class="w-100 border"
                                    style="cursor: pointer; aspect-ratio: 1/1; object-fit: cover;"
                                    alt="Miniatura {{ $index + 1 }}"
                                >
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="border rounded p-4 shadow-sm">
                    <h5 class="mb-2">{{ $property->title }}</h5>
                    <p class="text-muted mb-1">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $property->zona->nombre ?? ($property->location ?? 'Ubicación no disponible') }}
                    </p>
                    <p class="text-muted small mb-3">Ref: <strong>{{ $property->ref }}</strong></p>
                    <h4 class="text-primary">
                        <i class="fas fa-euro-sign me-1"></i>{{ number_format($property->price, 0, ',', '.') }}
                    </h4>

                    <hr class="my-3">

                    <div class="row text-center gy-3">
                        <div class="row my-4">
                            <div class="col-6">
                                <small class="text-muted"><i class="fas fa-home me-1"></i></small>
                                <div class="fw-semibold">{{ ucfirst($property->tipo ?? 'N/D') }}</div>
                            </div>

                            <div class="col-6">
                                <small class="text-muted"><i class="fas fa-location-dot me-1"></i></small>
                                <div class="fw-semibold">{{ $property->location ?? 'N/D' }}</div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-4">
                                <small class="text-muted"><i class="fas fa-bed me-1"></i></small>
                                <div class="fw-semibold">{{ $property->bedrooms ?? '-' }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted"><i class="fas fa-bath me-1"></i></small>
                                <div class="fw-semibold">{{ $property->bathrooms ?? '-' }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted"><i class="fas fa-ruler-combined me-1"></i></small>
                                <div class="fw-semibold">{{ $property->area ? number_format($property->area, 2, ',', '.') : '-' }} m²</div>
                            </div>
                        </div>

                        @if ($property->tiene_solar)
                            <div class="col-6">
                                <small class="text-muted"><i class="fas fa-border-none me-1"></i></small>
                                <div class="fw-semibold">{{ $property->metros_solar ? number_format($property->metros_solar, 2, ',', '.') : '-' }} m²</div>
                            </div>
                        @endif

                        @if ($property->tiene_patio)
                            <div class="col-6">
                                <small class="text-muted"><i class="fas fa-seedling me-1"></i></small>
                                <div class="fw-semibold">Jardín</div>
                            </div>
                        @endif

                        @if ($property->tiene_piscina)
                            <div class="col-6">
                                <small class="text-muted"><i class="fas fa-water me-1"></i></small>
                                <div class="fw-semibold">Piscina</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="mt-5">
                <h5 class="fw-semibold">Descripción</h5>
                <p style="white-space: pre-line;">{{ $property->description }}</p>
            </div>

            <div class="border rounded p-4 shadow-sm mb-4">
                <h5 class="mb-4">Solicita información</h5>
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                    <input type="hidden" name="message" value="Solicitud de información sobre la propiedad {{ $property->title }} ({{ $property->ref }})">

                    <div class="mb-3">
                        <label class="form-label">Tu nombre completo *</label>
                        <input type="text" name="name" class="form-control" placeholder="p.ej: María" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tu email *</label>
                        <input type="email" name="email" class="form-control" placeholder="p.ej: nombre@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tu teléfono</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+34">
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Enviar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
