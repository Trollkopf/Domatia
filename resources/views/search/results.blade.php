@extends('layouts.guest')

@section('title', 'Resultados de búsqueda')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">Resultados de la búsqueda</h2>

        <div class="row">
            @forelse($results as $property)
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <img src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}" class="card-img-top" alt="{{ $property->title }}">
                       <div class="card-body">
                            <h5 class="card-title">{{ $property->title }}</h5>
                            <p class="card-text">€{{ number_format($property->price, 0, ',', '.') }} ·
                                {{ $property->bedrooms }} hab · {{ $property->bathrooms }} baños</p>
                            <a href="{{ route('guest.property.show', $property->slug) }}" class="btn btn-outline-dark btn-sm">Ver más</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>No se encontraron propiedades que coincidan con la búsqueda.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $results->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
