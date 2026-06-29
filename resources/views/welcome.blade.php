@extends('layouts.guest')

@section('title', __('ui.home.title'))

@php
    $homeMetaTitle = trim($siteSettings['home_hero_title'] ?? __('ui.home.title'));
    $homeMetaDescription = \Illuminate\Support\Str::limit(
        trim(($siteSettings['home_hero_subtitle'] ?? '') . ' ' . ($siteSettings['home_featured_subtitle'] ?? '')),
        160
    );
    $homeMetaImage = collect([
        $siteSettings['home_hero_image_1'] ?? null,
        $siteSettings['home_hero_image_2'] ?? null,
        $siteSettings['home_hero_image_3'] ?? null,
    ])->filter()->first();
@endphp

@section('meta_title', $homeMetaTitle)
@section('meta_description', $homeMetaDescription ?: ($siteSettings['company_name'] ?? config('app.name', 'Domatia')))
@section('meta_image', $homeMetaImage ?: asset('images/our-company.jpg'))
@section('canonical', url()->current())
@section('meta_type', 'website')

@section('style')
<link href="{{ asset('css/slider.css') }}" rel="stylesheet">
<style>
    .hero-slide {
        height: 80vh;
        min-height: 620px;
    }

    .hero-slide-media {
        position: absolute;
        inset: 0;
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        transform: scale(1.02);
    }

    .hero-slide-overlay {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(180deg, rgba(15, 23, 42, 0.25) 0%, rgba(15, 23, 42, 0.55) 38%, rgba(15, 23, 42, 0.82) 100%),
            linear-gradient(90deg, rgba(17, 24, 39, 0.72) 0%, rgba(17, 24, 39, 0.34) 48%, rgba(17, 24, 39, 0.58) 100%);
    }

    .hero-copy {
        position: relative;
        z-index: 2;
        max-width: 960px;
        padding: 2rem;
        border-radius: 28px;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.3) 0%, rgba(15, 23, 42, 0.58) 100%);
        backdrop-filter: blur(10px);
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.28);
    }

    .hero-copy h1,
    .hero-copy p {
        text-shadow: 0 3px 18px rgba(0, 0, 0, 0.35);
    }

    .hero-search {
        border-radius: 22px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
    }

    .hero-value-card {
        background: rgba(15, 23, 42, 0.58);
        backdrop-filter: blur(8px);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.08);
    }

    .home-property-card {
        display: block;
        border-radius: 22px;
        overflow: hidden;
        background: #fff;
        color: inherit;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .home-property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 44px rgba(15, 23, 42, 0.14);
        color: inherit;
    }

    .home-property-card-media {
        aspect-ratio: 4 / 3;
        overflow: hidden;
    }

    .home-property-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 991.98px) {
        .hero-slide {
            min-height: 680px;
        }

        .hero-copy {
            padding: 1.5rem;
            border-radius: 22px;
        }
    }

    @media (max-width: 767.98px) {
        .hero-slide {
            min-height: 760px;
            height: auto;
        }

        .hero-copy {
            padding: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
    @php
        $heroCount = max(1, min(3, (int) ($siteSettings['home_hero_count'] ?? 3)));

        $heroImages = collect([
            $siteSettings['home_hero_image_1'],
            $siteSettings['home_hero_image_2'],
            $siteSettings['home_hero_image_3'],
        ])->filter()->take($heroCount)->values();

        if ($heroImages->isEmpty()) {
            $heroImages = collect([
                '/images/our-company.jpg',
                '/images/images.jpg',
                '/images/our-company.jpg',
            ])->take($heroCount);
        }

        $valueProps = collect([
            $siteSettings['home_value_1'],
            $siteSettings['home_value_2'],
            $siteSettings['home_value_3'],
        ])->filter()->values();
    @endphp

    <section class="swiper heroSwiper">
        <div class="swiper-wrapper">
            @foreach ($heroImages as $img)
                <div class="swiper-slide position-relative hero-slide">
                    <div class="hero-slide-media" style="background-image: url('{{ $img }}');"></div>
                    <div class="hero-slide-overlay"></div>

                    <div class="container h-100 d-flex align-items-center justify-content-center position-absolute top-0 start-0 end-0 bottom-0">
                        <div class="hero-copy text-center text-white">
                            @if (! empty($siteSettings['home_hero_badge']))
                                <span class="badge rounded-pill bg-light text-dark px-3 py-2 mb-3">{{ $siteSettings['home_hero_badge'] }}</span>
                            @endif

                            <h1 class="display-4 fw-light">{{ $siteSettings['home_hero_title'] }}</h1>
                            <p class="lead">{{ $siteSettings['home_hero_subtitle'] }}</p>

                            <form action="{{ route('search') }}" method="GET" class="hero-search row g-2 mt-4 bg-white p-3 text-dark">
                                <div class="col-md-3">
                                    <input type="text" name="location" class="form-control" placeholder="{{ __('ui.home.location_placeholder') }}">
                                </div>

                                <div class="col-md-3">
                                    <select name="type" class="form-select">
                                        <option value="">{{ __('ui.home.property_type') }}</option>
                                        <option value="Piso">{{ __('ui.property_types.piso') }}</option>
                                        <option value="Casa">{{ __('ui.property_types.casa') }}</option>
                                        <option value="Villa">{{ __('ui.property_types.villa') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="number" name="min_price" class="form-control" placeholder="{{ __('ui.home.min_price') }}">
                                </div>

                                <div class="col-md-2">
                                    <input type="number" name="max_price" class="form-control" placeholder="{{ __('ui.home.max_price') }}">
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-dark w-100">
                                        {{ $siteSettings['home_search_button_text'] }}
                                    </button>
                                </div>
                            </form>

                            @if ($valueProps->isNotEmpty())
                                <div class="row g-2 mt-3 text-start">
                                    @foreach ($valueProps as $value)
                                        <div class="col-md-4">
                                            <div class="hero-value-card rounded-3 px-3 py-2 h-100">
                                                <span class="small fw-semibold">{{ $value }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="pt-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="mb-2">{{ $siteSettings['home_featured_heading'] }}</h2>

                @if (! empty($siteSettings['home_featured_subtitle']))
                    <p class="text-muted mb-0">{{ $siteSettings['home_featured_subtitle'] }}</p>
                @endif
            </div>

            @if ($featured->isNotEmpty())
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($featured as $property)
                            <div class="swiper-slide">
                                <a href="{{ route('guest.property.show', $property->slug) }}" class="d-block position-relative" style="aspect-ratio: 1 / 1; overflow: hidden;">
                                    <img
                                        src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                        alt="{{ $property->title }}"
                                    >
                                    <div class="position-absolute bottom-0 start-0 end-0 p-2 text-white" style="background: linear-gradient(to top, rgba(0, 0, 0, 0.6), transparent);">
                                        <div class="fw-semibold small">{{ $property->translatedTitle() }}</div>
                                        <div class="small">{{ number_format($property->price, 0, ',', '.') }} EUR</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            @else
                <div class="rounded-4 border bg-light p-4 text-center">
                    <p class="text-muted mb-3">{{ __('ui.properties.empty') }}</p>
                    <a href="{{ route('guest.properties.index') }}" class="btn btn-outline-dark">{{ __('ui.common.view_catalog') }}</a>
                </div>
            @endif
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <h2 class="mb-2">{{ __('ui.home.latest_heading') }}</h2>
                    <p class="text-muted mb-0">{{ __('ui.home.latest_subtitle') }}</p>
                </div>

                <a href="{{ route('guest.properties.index') }}" class="btn btn-outline-dark">{{ __('ui.common.view_catalog') }}</a>
            </div>

            <div class="row g-4">
                @forelse ($latestProperties as $property)
                    <div class="col-md-6 col-xl-4">
                        <a href="{{ route('guest.property.show', $property->slug) }}" class="home-property-card h-100">
                            <div class="home-property-card-media">
                                <img
                                    src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}"
                                        alt="{{ $property->translatedTitle() }}"
                                >
                            </div>

                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <h3 class="h5 mb-0">{{ $property->translatedTitle() }}</h3>
                                    @if ($property->is_featured)
                                        <span class="badge bg-warning text-dark">{{ __('ui.common.featured') }}</span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-3">
                                    {{ $property->translatedLocation() ?? __('ui.properties.location_pending') }}
                                    @if ($property->tipo)
                                        {{ '· ' . $property->translatedTypeLabel() }}
                                    @endif
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ number_format($property->price, 0, ',', '.') }} EUR</span>
                                    <span class="small text-muted">{{ $property->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="rounded-4 border bg-white p-4 text-center">
                            <p class="text-muted mb-3">{{ __('ui.properties.empty') }}</p>
                            <a href="{{ route('guest.properties.index') }}" class="btn btn-outline-dark">{{ __('ui.common.explore_properties') }}</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="rounded-4 p-4 p-lg-5 text-white" style="background: linear-gradient(135deg, #1f2937 0%, #374151 100%);">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="mb-3">{{ $siteSettings['home_cta_heading'] }}</h2>
                        <p class="mb-0 opacity-75">{{ $siteSettings['home_cta_body'] }}</p>
                    </div>

                    <div class="col-lg-4">
                        <div class="d-grid gap-2">
                            <a href="{{ $siteSettings['home_cta_primary_url'] ?: route('guest.properties.index') }}" class="btn btn-light">
                                {{ $siteSettings['home_cta_primary_text'] ?: __('ui.common.view_catalog') }}
                            </a>

                            <a href="{{ $siteSettings['home_cta_secondary_url'] ?: route('contact') }}" class="btn btn-outline-light">
                                {{ $siteSettings['home_cta_secondary_text'] ?: __('ui.nav.contact') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Swiper(".heroSwiper", {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                }
            });

            if (document.querySelector(".mySwiper")) {
                new Swiper(".mySwiper", {
                    slidesPerView: 4,
                    spaceBetween: 24,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        0: { slidesPerView: 1.2 },
                        576: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        992: { slidesPerView: 4 },
                    },
                });
            }
        });
    </script>
    @endpush

    @push('structured_data')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteSettings['company_name'] ?? config('app.name', 'Domatia'),
                'url' => url('/'),
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('guest.properties.index') . '?search={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush
@endsection
