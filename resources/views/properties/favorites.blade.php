@extends('layouts.guest')

@section('title', __('ui.properties.favorites_title'))
@section('meta_title', __('ui.properties.favorites_title'))
@section('meta_description', __('ui.properties.favorites_intro'))
@section('canonical', route('guest.properties.favorites'))
@section('meta_robots', 'noindex,follow')

@section('style')
    <style>
        .favorites-hero {
            background:
                radial-gradient(circle at top right, rgba(212, 165, 45, 0.12), transparent 24%),
                linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }

        .favorites-shell .property-teaser-card {
            height: 100%;
        }
    </style>
@endsection

@section('content')
    <section class="favorites-hero py-5 border-bottom">
        <div class="container">
            <div class="row align-items-end g-3">
                <div class="col-lg-8">
                    <h1 class="display-6 mb-2">{{ __('ui.properties.favorites_title') }}</h1>
                    <p class="text-muted mb-0">
                        {{ __('ui.properties.favorites_intro') }}
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <span class="badge rounded-pill text-bg-dark px-3 py-2">
                        {{ $properties->count() }} guardada{{ $properties->count() === 1 ? '' : 's' }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="favorites-shell py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            @if ($properties->isNotEmpty())
                <div class="row g-4">
                    @foreach ($properties as $property)
                        <div class="col-md-6 col-xl-4">
                            @include('properties._property-card', ['property' => $property])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-4 border bg-white p-5 text-center shadow-sm">
                    <h2 class="h4 mb-3">{{ __('ui.properties.favorites_empty_title') }}</h2>
                    <p class="text-muted mb-4">
                        {{ __('ui.properties.favorites_empty_body') }}
                    </p>
                    <a href="{{ route('guest.properties.index') }}" class="btn btn-dark">{{ __('ui.common.explore_properties') }}</a>
                </div>
            @endif
        </div>
    </section>
@endsection
