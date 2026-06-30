@extends('layouts.guest')

@section('title', $property->translatedTitle())

@php
    $propertySeoTitle = $property->translatedTitle();
    $propertySeoDescription = \Illuminate\Support\Str::limit(
        trim(($property->translatedDescription() ?: '') . ' ' . collect($property->quickSummaryItems())->implode(' ')),
        160
    );
    $propertySeoImage = $galleryImages->first() ? asset('storage/' . $galleryImages->first()) : asset('images/our-company.jpg');
@endphp

@section('meta_title', $propertySeoTitle)
@section('meta_description', $propertySeoDescription)
@section('meta_image', $propertySeoImage)
@section('canonical', route('guest.property.show', $property->slug))
@section('meta_type', 'article')

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

        .property-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.9rem;
        }

        .property-detail-item {
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 0.95rem 1rem;
        }

        .property-map {
            width: 100%;
            height: 340px;
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
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

            .property-stat-grid,
            .property-detail-grid {
                grid-template-columns: 1fr;
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
            $property->tiene_piscina ? ['icon' => 'fa-water-ladder', 'label' => __('frontend.properties.features.pool')] : null,
            $property->tiene_patio ? ['icon' => 'fa-seedling', 'label' => __('frontend.properties.features.patio')] : null,
            $property->tiene_solar ? ['icon' => 'fa-vector-square', 'label' => __('frontend.properties.features.plot_available')] : null,
            $property->metros_solar ? ['icon' => 'fa-border-all', 'label' => __('frontend.properties.features.plot_area', ['area' => number_format($property->metros_solar, 0, ',', '.')])] : null,
            $property->has_air_conditioning ? ['icon' => 'fa-snowflake', 'label' => __('frontend.properties.features.air_conditioning')] : null,
            $property->has_garage ? ['icon' => 'fa-warehouse', 'label' => __('frontend.properties.features.garage')] : null,
            $property->has_lift ? ['icon' => 'fa-elevator', 'label' => __('frontend.properties.features.lift')] : null,
            $property->has_parking ? ['icon' => 'fa-square-parking', 'label' => __('frontend.properties.features.parking')] : null,
            $property->has_terrace ? ['icon' => 'fa-sun', 'label' => __('frontend.properties.features.terrace')] : null,
            $property->has_garden ? ['icon' => 'fa-tree', 'label' => __('frontend.properties.features.garden')] : null,
            $property->has_solarium ? ['icon' => 'fa-sun-plant-wilt', 'label' => __('frontend.properties.features.solarium')] : null,
            $property->has_storage_room ? ['icon' => 'fa-box-open', 'label' => __('frontend.properties.features.storage_room')] : null,
            $property->is_furnished ? ['icon' => 'fa-couch', 'label' => __('frontend.properties.features.furnished')] : null,
            $property->has_sea_views ? ['icon' => 'fa-water', 'label' => __('frontend.properties.features.sea_views')] : null,
            $property->new_build ? ['icon' => 'fa-building-circle-check', 'label' => __('frontend.properties.features.new_build')] : null,
            $property->is_featured ? ['icon' => 'fa-star', 'label' => __('frontend.properties.features.featured')] : null,
        ])->filter()->values();
        $propertyDetails = collect([
            ['label' => __('frontend.properties.details.town'), 'value' => $property->town],
            ['label' => __('frontend.properties.details.province'), 'value' => $property->province],
            ['label' => __('frontend.properties.details.country'), 'value' => $property->country],
            ['label' => __('frontend.properties.details.location_detail'), 'value' => $property->location_detail],
            ['label' => __('frontend.properties.details.price'), 'value' => $property->price ? number_format($property->price, 0, ',', '.') . ' ' . ($property->currency ?: 'EUR') : null],
            ['label' => __('frontend.properties.details.operation'), 'value' => $property->price_freq],
            ['label' => __('frontend.properties.details.energy_consumption'), 'value' => $property->energy_consumption],
            ['label' => __('frontend.properties.details.emissions'), 'value' => $property->energy_emissions],
            ['label' => __('frontend.properties.details.reference'), 'value' => $property->ref],
            ['label' => __('frontend.properties.details.source_date'), 'value' => $property->source_date?->format('d/m/Y H:i')],
        ])->filter(fn (array $item) => filled($item['value']))->values();
        $featureList = collect($property->translatedFeaturesList())->values();
    @endphp

    <div class="property-show-shell py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <a href="{{ $mainImage }}" id="property-main-link" class="glightbox property-gallery-stage" data-gallery="property-gallery">
                        <div class="property-gallery-badge">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $locationLabel }}</span>
                        </div>

                        <img id="property-main-image" src="{{ $mainImage }}" alt="{{ $property->translatedTitle() }}">

                        <div class="property-gallery-count">
                            {{ trans_choice('frontend.common.photos', max($galleryImages->count(), 1), ['count' => max($galleryImages->count(), 1)]) }}
                        </div>
                    </a>

                    @if ($galleryImages->isNotEmpty())
                        <div class="property-thumbs">
                            @foreach ($galleryImages as $imagePath)
                                @php
                                    $imageUrl = asset('storage/' . $imagePath);
                                @endphp
                                <button type="button" class="property-thumb {{ $loop->first ? 'is-active' : '' }}" data-property-thumb data-image-url="{{ $imageUrl }}">
                                    <img src="{{ $imageUrl }}" alt="{{ __('frontend.common.image_of', ['title' => $property->translatedTitle()]) }}">
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
                                    {{ __('frontend.common.reference_short') }} {{ $property->ref ?: __('frontend.common.pending') }}
                                </span>
                                <h1 class="display-6 mt-3 mb-2">{{ $property->translatedTitle() }}</h1>
                                <p class="text-muted mb-0">{{ __('frontend.properties.type_in_location', ['type' => $propertyType, 'location' => $locationLabel]) }}</p>
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

                        <div class="property-price fw-semibold mb-3">{{ number_format($property->price, 0, ',', '.') }} {{ $property->currency ?: 'EUR' }}</div>

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
                                    {{ $property->area ? number_format($property->area, 0, ',', '.') . ' m²' : __('ui.properties.featured_space') }}
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
                            <a href="{{ route('guest.properties.favorites') }}" class="btn btn-outline-secondary">{{ __('ui.common.view_favorites') }}</a>
                            @if ($property->video_url)
                                <a href="{{ $property->video_url }}" class="btn btn-outline-dark" target="_blank" rel="noopener noreferrer">{{ __('frontend.properties.video') }}</a>
                            @endif
                            @if ($property->virtual_tour_url)
                                <a href="{{ $property->virtual_tour_url }}" class="btn btn-outline-dark" target="_blank" rel="noopener noreferrer">{{ __('frontend.properties.virtual_tour') }}</a>
                            @endif
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
                                <p class="text-muted mb-0">{{ __('ui.properties.description_intro') }}</p>
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

                    @if ($propertyDetails->isNotEmpty())
                        <section class="property-content-card mb-4">
                            <div class="property-section-title mb-3">{{ __('frontend.properties.technical_sheet') }}</div>
                            <div class="property-detail-grid">
                                @foreach ($propertyDetails as $detail)
                                    <div class="property-detail-item">
                                        <div class="small text-uppercase text-muted mb-1">{{ $detail['label'] }}</div>
                                        <div class="fw-semibold">{{ $detail['value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($featureList->isNotEmpty())
                        <section class="property-content-card mb-4">
                            <div class="property-section-title mb-3">{{ __('frontend.properties.highlighted_features') }}</div>
                            <div class="property-pills">
                                @foreach ($featureList as $feature)
                                    <span class="property-pill">
                                        <i class="fa-solid fa-check"></i>
                                        <span>{{ $feature }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($property->hasCoordinates())
                        <section class="property-content-card mb-4">
                            <div class="property-section-title mb-3">{{ __('frontend.properties.map_location') }}</div>
                            <div id="map" class="property-map" data-latitude="{{ $property->latitude }}" data-longitude="{{ $property->longitude }}" data-zoom="14" data-title="{{ $property->translatedTitle() }}"></div>
                        </section>
                    @endif

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
                        <p class="text-muted mb-4">{{ __('ui.properties.contact_intro', ['title' => $property->translatedTitle()]) }}</p>

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">
                            <input type="hidden" name="message" value="{{ __('frontend.properties.contact_default_message', ['title' => $property->translatedTitle(), 'reference' => $property->ref ?: __('frontend.common.pending')]) }}">

                            <div class="mb-3">
                                <label class="form-label">{{ __('ui.properties.name') }} *</label>
                                <input type="text" name="name" class="form-control" placeholder="{{ __('frontend.properties.name_placeholder') }}" required>
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
                                <label class="form-check-label" for="property_accept_terms">{{ __('ui.properties.accept_terms') }}</label>
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

    @push('structured_data')
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'RealEstateListing',
                'name' => $property->translatedTitle(),
                'description' => $propertySeoDescription,
                'url' => route('guest.property.show', $property->slug),
                'image' => $galleryImages->map(fn ($imagePath) => asset('storage/' . $imagePath))->values()->all() ?: [$propertySeoImage],
                'datePosted' => optional($property->source_date ?: $property->created_at)->toAtomString(),
                'inLanguage' => str_replace('_', '-', app()->getLocale()),
                'identifier' => $property->ref ?: $property->slug,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $property->price,
                    'priceCurrency' => $property->currency ?: 'EUR',
                    'availability' => 'https://schema.org/' . ($property->status === 'published' ? 'InStock' : 'SoldOut'),
                    'url' => route('guest.property.show', $property->slug),
                ],
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $property->town ?: ($property->translatedLocation() ?: $property->zona?->translatedName()),
                    'addressRegion' => $property->province ?: $property->zona?->translatedName(),
                    'addressCountry' => $property->country ?: 'ES',
                ],
                'geo' => $property->hasCoordinates() ? [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                ] : null,
                'numberOfRooms' => $property->bedrooms ?: null,
                'numberOfBathroomsTotal' => $property->bathrooms ?: null,
                'floorSize' => $property->area ? [
                    '@type' => 'QuantitativeValue',
                    'value' => $property->area,
                    'unitCode' => 'MTK',
                ] : null,
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
                        'name' => __('ui.properties.page_title'),
                        'item' => route('guest.properties.index'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $property->translatedTitle(),
                        'item' => route('guest.property.show', $property->slug),
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush
@endsection
