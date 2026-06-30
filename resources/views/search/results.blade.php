@extends('layouts.guest')

@section('title', __('ui.search.title'))

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">{{ __('ui.search.heading') }}</h2>

        <div class="row">
            @forelse($results as $property)
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <img src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}" class="card-img-top" alt="{{ __('frontend.common.image_of', ['title' => $property->translatedTitle()]) }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $property->translatedTitle() }}</h5>
                            <p class="card-text">
                                EUR {{ number_format($property->price, 0, ',', '.') }} ·
                                {{ $property->bedrooms }} {{ __('ui.properties.stats.bedrooms_short') }} ·
                                {{ $property->bathrooms }} {{ __('ui.properties.stats.bathrooms_short') }}
                            </p>
                            <a href="{{ route('guest.property.show', $property->slug) }}" class="btn btn-outline-dark btn-sm">{{ __('ui.search.view_more') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>{{ __('ui.search.empty') }}</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $results->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
