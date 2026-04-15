@extends('layouts.guest')

@section('title', $property->translatedTitle())

@section('style')
    <style>
        .property-show-shell {
            background:
                radial-gradient(circle at top left, rgba(212, 165, 45, 0.08), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #ffffff 38%);
        }

        .property-gallery-stage {
            position: relative;
            display: block;
            overflow: hidden;
            border-radius: 28px;
            height: 540px;
            min-height: 540px;
            background: #0f172a;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.14);
        }

        .property-gallery-stage img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-gallery-badge {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.6rem 0.9rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.72);
            color: #fff;
            backdrop-filter: blur(10px);
        }

        .property-gallery-count {
            position: absolute;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 2;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #0f172a;
            font-weight: 600;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
        }

        .property-thumbs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 0.85rem;
            margin-top: 1rem;
        }

        .property-thumb {
            border: 0;
            padding: 0;
            overflow: hidden;
            border-radius: 18px;
            background: #e2e8f0;
            aspect-ratio: 1 / 1;
            box-shadow: inset 0 0 0 2px transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .property-thumb:hover,
        .property-thumb.is-active {
            transform: translateY(-2px);
            box-shadow: inset 0 0 0 2px #d4a52d;
        }

        .property-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-summary-card,
        .property-content-card,
        .property-contact-card {
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        }

        .property-summary-card {
            position: sticky;
            top: 1.5rem;
            padding: 1.75rem;
        }

        .property-reference {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            background: #f8fafc;
            color: #475569;
            font-size: 0.9rem;
        }

        .property-price {
            font-size: clamp(2rem, 4vw, 2.9rem);
            line-height: 1;
            color: #111827;
        }

        .property-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
        }

        .property-stat {
            border-radius: 20px;
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .property-stat-label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .property-stat-value {
            font-weight: 600;
            color: #0f172a;
        }

        .property-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .property-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.7rem 0.95rem;
            border-radius: 999px;
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            font-weight: 500;
        }

        .property-content-card,
        .property-contact-card {
            padding: 1.75rem;
        }

        .property-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f172a;
        }

        .property-lead {
            color: #475569;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .property-highlights {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .property-highlight {
            padding: 1.1rem;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
        }

        .property-highlight i {
            color: #d4a52d;
        }

        .property-favorite-form .btn {
            border-radius: 999px;
        }

        .property-teaser-card {
            overflow: hidden;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid #e2e8f0;
        }

        .property-teaser-media {
            display: block;
            aspect-ratio: 4 / 3;
            overflow: hidden;
        }

        .property-teaser-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-teaser-body {
            padding: 1.25rem;
        }

        .property-teaser-location {
            color: #64748b;
            font-size: 0.9rem;
        }

        .property-teaser-price {
            white-space: nowrap;
            font-weight: 700;
            color: #111827;
        }

        .property-teaser-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            color: #475569;
            font-size: 0.92rem;
        }

        @media (max-width: 991.98px) {
            .property-gallery-stage {
                height: 420px;
                min-height: 420px;
            }

            .property-summary-card {
                position: static;
            }

            .property-highlights {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .property-gallery-stage {
                height: 320px;
                min-height: 320px;
                border-radius: 22px;
            }

            .property-summary-card,
            .property-content-card,
            .property-contact-card {
                border-radius: 22px;
                padding: 1.25rem;
            }

            .property-stat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $locationLabel = $property->zona?->translatedName() ?? ($property->translatedLocation() ?: __('ui.properties.location_pending'));
        $propertyType = $property->translatedTypeLabel();
        $mainImage = $galleryImages->first() ? asset('storage/' . $galleryImages->first()) : asset('images/our-company.jpg');
        $isFavorite = in_array($property->slug, $favoriteSlugs ?? [], true);
        $quickSummaryItems = $property->quickSummaryItems();
        $quickSummaryMeta = collect([
            ['icon' => 'fa-house', 'title' => __('ui.properties.summary_labels.type')],
            ['icon' => 'fa-maximize', 'title' => __('ui.properties.summary_labels.space')],
            ['icon' => 'fa-key', 'title' => __('ui.properties.summary_labels.operation')],
        ]);

        $featurePills = collect([
            $property->tiene_piscina ? ['icon' => 'fa-water-ladder', 'label' => 'Piscina'] : null,
            $property->tiene_patio ? ['icon' => 'fa-seedling', 'label' => 'Jardin o patio'] : null,
            $property->tiene_solar ? ['icon' => 'fa-vector-square', 'label' => 'Solar disponible'] : null,
            $property->metros_solar ? ['icon' => 'fa-border-all', 'label' => number_format($property->metros_solar, 0, ',', '.') . ' m2 de parcela'] : null,
            $property->is_featured ? ['icon' => 'fa-star', 'label' => 'Propiedad destacada'] : null,
        ])->filter()->values();
    @endphp

    <div class="property-show-shell py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <a
                        href="{{ $mainImage }}"
                        id="property-main-link"
                        class="glightbox property-gallery-stage"
                        data-gallery="property-gallery"
                    >
                        <div class="property-gallery-badge">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $locationLabel }}</span>
                        </div>

                        <img id="property-main-image" src="{{ $mainImage }}" alt="{{ $property->translatedTitle() }}">

                        <div class="property-gallery-count">
                            {{ max($galleryImages->count(), 1) }} foto{{ max($galleryImages->count(), 1) === 1 ? '' : 's' }}
                        </div>
                    </a>

                    @if ($galleryImages->isNotEmpty())
                        <div class="property-thumbs">
                            @foreach ($galleryImages as $imagePath)
                                @php
                                    $imageUrl = asset('storage/' . $imagePath);
                                @endphp
                                <button
                                    type="button"
                                    class="property-thumb {{ $loop->first ? 'is-active' : '' }}"
                                    data-property-thumb
                                    data-image-url="{{ $imageUrl }}"
                                >
                                    <img src="{{ $imageUrl }}" alt="Imagen de {{ $property->translatedTitle() }}">
                                </button>
                                <a href="{{ $imageUrl }}" class="glightbox d-none" data-gallery="property-gallery"></a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-5">
                    <aside class="property-summary-card">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <span class="property-reference mb-3">
                                    <i class="fa-solid fa-hashtag"></i>
                                    Ref. {{ $property->ref ?: 'Pendiente' }}
                                </span>
                                <h1 class="display-6 mt-3 mb-2">{{ $property->translatedTitle() }}</h1>
                                <p class="text-muted mb-0">{{ $propertyType }} en {{ $locationLabel }}</p>
                            </div>

                            <form action="{{ route('guest.property.favorite', $property->slug) }}" method="POST" class="property-favorite-form" data-favorite-toggle-form data-property-slug="{{ $property->slug }}">
                                @csrf
                                <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
                                <button type="submit" class="btn {{ $isFavorite ? 'btn-dark' : 'btn-outline-dark' }}" data-favorite-toggle-button aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">
                                    <i class="fa-{{ $isFavorite ? 'solid' : 'regular' }} fa-heart me-2" data-favorite-toggle-icon></i>
                                    <span data-favorite-toggle-label>{{ $isFavorite ? __('ui.properties.saved') : __('ui.properties.favorite') }}</span>
                                </button>
                            </form>
                        </div>

                        <div class="property-price fw-semibold mb-3">{{ number_format($property->price, 0, ',', '.') }} EUR</div>

                        <div class="property-stat-grid mb-4">
                            <div class="property-stat">
                                <span class="property-stat-label">{{ __('ui.properties.bedrooms') }}</span>
                                <div class="property-stat-value">{{ $property->bedrooms ?: '-' }}</div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">{{ __('ui.properties.bathrooms') }}</span>
                                <div class="property-stat-value">{{ $property->bathrooms ?: '-' }}</div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">{{ __('ui.properties.surface') }}</span>
                                <div class="property-stat-value">
                                    {{ $property->area ? number_format($property->area, 0, ',', '.') . ' m2' : __('ui.properties.featured_space') }}
                                </div>
                            </div>
                            <div class="property-stat">
                                <span class="property-stat-label">{{ __('ui.properties.type') }}</span>
                                <div class="property-stat-value">{{ $propertyType }}</div>
                            </div>
                        </div>

                        @if ($featurePills->isNotEmpty())
                            <div class="property-pills mb-4">
                                @foreach ($featurePills as $pill)
                                    <span class="property-pill">
                                        <i class="fa-solid {{ $pill['icon'] }}"></i>
                                        <span>{{ $pill['label'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <a href="#property-contact" class="btn btn-dark btn-lg">{{ __('ui.properties.request_information') }}</a>
                            <a href="{{ route('guest.properties.favorites') }}" class="btn btn-outline-secondary">
                                {{ __('ui.common.view_favorites') }}
                            </a>
                        </div>
                    </aside>
                </div>
            </div>

            <div class="row g-4 mt-2">
                <div class="col-lg-8">
                    <section class="property-content-card mb-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <div class="property-section-title mb-2">{{ __('ui.properties.description_title') }}</div>
                                <p class="text-muted mb-0">
                                    {{ __('ui.properties.description_intro') }}
                                </p>
                            </div>
                            <span class="property-reference">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $locationLabel }}
                            </span>
                        </div>

                        <p class="property-lead mb-0" style="white-space: pre-line;">
                            {{ $property->translatedDescription() ?: __('ui.properties.description_pending') }}
                        </p>
                    </section>

                    <section class="property-content-card mb-4">
                        <div class="property-section-title mb-3">{{ __('ui.properties.summary_title') }}</div>
                        <div class="property-highlights">
                            @foreach ($quickSummaryItems as $index => $summaryItem)
                                @php
                                    $summaryMeta = $quickSummaryMeta[$index] ?? ['icon' => 'fa-circle-info', 'title' => __('ui.properties.summary_labels.default')];
                                @endphp
                                <div class="property-highlight">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fa-solid {{ $summaryMeta['icon'] }}"></i>
                                        <strong>{{ $summaryMeta['title'] }}</strong>
                                    </div>
                                    <p class="text-muted mb-0">{{ $summaryItem }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    @if ($relatedProperties->isNotEmpty())
                        <section class="property-content-card">
                            <div class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
                                <div>
                                    <div class="property-section-title mb-2">{{ __('ui.properties.related') }}</div>
                                    <p class="text-muted mb-0">{{ __('ui.properties.related_subtitle') }}</p>
                                </div>
                                <a href="{{ route('guest.properties.index') }}" class="btn btn-outline-dark">{{ __('ui.common.view_catalog') }}</a>
                            </div>

                            <div class="row g-4">
                                @foreach ($relatedProperties as $relatedProperty)
                                    <div class="col-md-6 col-xl-4">
                                        @include('properties._property-card', ['property' => $relatedProperty])
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <div class="col-lg-4">
                    <section class="property-contact-card" id="property-contact">
                        <div class="property-section-title mb-2">{{ __('ui.properties.contact_title') }}</div>
                        <p class="text-muted mb-4">
                            {{ __('ui.properties.contact_intro', ['title' => $property->translatedTitle()]) }}
                        </p>

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">
                            <input type="hidden" name="message" value="Solicitud de informacion sobre la propiedad {{ $property->translatedTitle() }} ({{ $property->ref }})">

                            <div class="mb-3">
                                <label class="form-label">{{ __('ui.properties.name') }} *</label>
                                <input type="text" name="name" class="form-control" placeholder="Tu nombre" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('ui.properties.email') }} *</label>
                                <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('ui.properties.phone') }}</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+34">
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="property_accept_terms" name="accept_terms" required>
                                <label class="form-check-label" for="property_accept_terms">
                                    {{ __('ui.properties.accept_terms') }}
                                </label>
                            </div>

                            <button type="submit" class="btn btn-dark w-100">{{ __('ui.common.send_request') }}</button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mainImage = document.getElementById('property-main-image');
                const mainLink = document.getElementById('property-main-link');
                const thumbs = document.querySelectorAll('[data-property-thumb]');

                thumbs.forEach(function (thumb) {
                    thumb.addEventListener('click', function () {
                        if (!mainImage) {
                            return;
                        }

                        mainImage.src = thumb.dataset.imageUrl;
                        if (mainLink) {
                            mainLink.href = thumb.dataset.imageUrl;
                        }

                        thumbs.forEach(function (item) {
                            item.classList.remove('is-active');
                        });

                        thumb.classList.add('is-active');
                    });
                });
            });
        </script>
    @endpush
@endsection
