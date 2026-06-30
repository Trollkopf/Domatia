@extends('layouts.guest')

@section('title', $zona->translatedName())

@php
    $zonaSeoTitle = $zona->translatedName();
    $zonaSectionDescription = $zona->secciones
        ->map(fn ($seccion) => $seccion->translatedDescription())
        ->filter()
        ->implode(' ');
    $zonaSeoDescription = \Illuminate\Support\Str::limit(
        trim($zonaSectionDescription) ?: __('ui.environment.properties_in', ['name' => $zona->translatedName()]),
        160
    );
    $zonaSeoImage = $zona->imageUrl();
@endphp

@section('meta_title', $zonaSeoTitle)
@section('meta_description', $zonaSeoDescription)
@section('meta_image', $zonaSeoImage)
@section('canonical', route('zonas.show', $zona->slug))
@section('meta_type', 'article')

@section('content')
    <section class="page-hero" style="min-height: 400px;">
        <img src="{{ $zona->imageUrl() }}" alt="{{ $zona->translatedName() }}" class="page-hero-media">
        <div class="page-hero-overlay"></div>
        <div class="container page-hero-content">
            <div class="page-hero-copy text-center mx-auto">
                <h1 class="display-4 fw-semibold">{{ $zona->translatedName() }}</h1>
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
                    <img src="{{ asset('storage/' . $seccion->imagen) }}" alt="{{ $seccion->translatedTitle() }}"
                        class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-6 mt-3 mt-md-0">
                    <h3 class="fw-semibold">{{ $seccion->translatedTitle() }}</h3>
                    <p class="text-muted" style="white-space: pre-line;">
                        {{ $seccion->translatedDescription() }}
                    </p>
                </div>
            </div>
        @endforeach
    </section>

    @if ($zona->publishedProperties->count())
        <section class="container py-5">
            <h2 class="mb-4">{{ __('ui.environment.properties_in', ['name' => $zona->translatedName()]) }}</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                @foreach ($zona->publishedProperties as $property)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}" class="card-img-top"
                                alt="{{ __('frontend.common.image_of', ['title' => $property->translatedTitle()]) }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $property->translatedTitle() }}</h5>
                                <p class="card-text">{{ Str::limit($property->translatedDescription(), 100) }}</p>
                                <a href="{{ route('guest.property.show', $property->slug) }}" class="btn btn-dark">{{ __('ui.environment.view_more') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @push('structured_data')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Place',
                'name' => $zona->translatedName(),
                'description' => $zonaSeoDescription,
                'url' => route('zonas.show', $zona->slug),
                'image' => $zonaSeoImage,
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>

        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $siteSettings['company_name'] ?? config('app.name', 'Domatia'),
                        'item' => url('/'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => __('ui.environment.title'),
                        'item' => route('environment'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $zona->translatedName(),
                        'item' => route('zonas.show', $zona->slug),
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush
@endsection
