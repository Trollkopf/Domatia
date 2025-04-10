@extends('layouts.guest')

@section('title', 'Inicio')

@section('content')
    {{-- Hero principal --}}
    <section class="position-relative bg-dark text-white"
        style="height: 80vh; background: url('/images/hero.jpg') center center / cover no-repeat;">
        <div class="container h-100 d-flex align-items-center justify-content-center">
            <div class="text-center">
                <h1 class="display-4 fw-light">Descubre propiedades exclusivas</h1>
                <p class="lead">En los destinos más deseados</p>

                {{-- Buscador --}}
                <form action="{{ route('search') }}" method="GET" class="row g-2 mt-4 bg-white p-3 rounded shadow text-dark">
                    <div class="col-md-3">
                        <input type="text" name="location" class="form-control" placeholder="Ubicación">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">Tipo de propiedad</option>
                            <option value="piso">Piso</option>
                            <option value="casa">Casa</option>
                            <option value="villa">Villa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="min_price" class="form-control" placeholder="Desde €">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="max_price" class="form-control" placeholder="Hasta €">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Sección futura: propiedades destacadas --}}
    <section class="pt-5">
        <h2 class="mb-4 text-center">Propiedades destacadas</h2>

        <div class="container">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach ($featured as $property)
                        <div class="swiper-slide">
                            <a href="{{ route('guest.property.show', $property->slug) }}" class="d-block position-relative"
                                style="aspect-ratio: 1/1; overflow: hidden;">
                                <img src="{{ $property->image }}" class="w-100 h-100" style="object-fit: cover;"
                                    alt="{{ $property->title }}">
                                <div class="position-absolute bottom-0 start-0 end-0 p-2 text-white"
                                    style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
                                    <div class="fw-semibold small">{{ $property->title }}</div>
                                    <div class="small">{{ number_format($property->price, 0, ',', '.') }} €</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Controles --}}
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                {{-- <div class="swiper-pagination"></div> --}}
            </div>
        </div>
    </section>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper(".mySwiper", {
                    slidesPerView: 4,
                    spaceBetween: 24,
                    // pagination: {
                    //     el: ".swiper-pagination",
                    //     clickable: true,
                    // },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1.2,
                        },
                        576: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 3,
                        },
                        992: {
                            slidesPerView: 4,
                        },
                    },
                });
            });
        </script>
    @endpush

@endsection
