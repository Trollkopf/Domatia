@extends('layouts.guest')

@section('title', __('ui.properties.page_title'))

@section('style')
<style>
    :root {
        --properties-ink: #182230;
        --properties-muted: #6b7280;
        --properties-line: #d9e2ec;
        --properties-sand: #f7f3eb;
        --properties-panel: rgba(255, 255, 255, 0.92);
        --properties-accent: #b88a3b;
        --properties-accent-soft: rgba(184, 138, 59, 0.12);
        --properties-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
    }

    .properties-index-shell {
        background:
            radial-gradient(circle at top left, rgba(184, 138, 59, 0.15), transparent 24%),
            radial-gradient(circle at top right, rgba(15, 23, 42, 0.06), transparent 20%),
            linear-gradient(180deg, #f7f4ee 0%, #ffffff 38%);
        min-height: calc(100vh - 120px);
    }

    .properties-hero {
        border: 1px solid rgba(217, 226, 236, 0.9);
        border-radius: 32px;
        background:
            linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(255, 248, 237, 0.92)),
            var(--properties-panel);
        box-shadow: var(--properties-shadow);
        padding: 2rem;
        margin-bottom: 1.75rem;
    }

    .properties-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.75);
        border: 1px solid rgba(184, 138, 59, 0.18);
        color: #8a6730;
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 700;
    }

    .properties-title {
        font-size: clamp(2rem, 2.7vw, 3.2rem);
        line-height: 1.02;
        letter-spacing: -0.04em;
        color: var(--properties-ink);
        margin: 1rem 0 0.9rem;
        max-width: 12ch;
    }

    .properties-intro {
        max-width: 62ch;
        color: #526071;
        font-size: 1.02rem;
        margin: 0;
    }

    .properties-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: flex-end;
        align-items: flex-start;
    }

    .properties-chip,
    .properties-hero-actions .btn {
        border-radius: 999px;
        padding: 0.85rem 1.1rem;
        font-weight: 600;
    }

    .properties-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid var(--properties-line);
        color: var(--properties-ink);
        min-width: 9rem;
    }

    .property-list-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.35rem;
    }

    .properties-toolbar,
    .properties-filter-card {
        border: 1px solid rgba(217, 226, 236, 0.92);
        border-radius: 28px;
        background: var(--properties-panel);
        box-shadow: var(--properties-shadow);
        backdrop-filter: blur(16px);
    }

    .properties-toolbar {
        padding: 1rem 1.15rem;
    }

    .properties-filter-card {
        padding: 1.2rem;
        position: sticky;
        top: 1.2rem;
    }

    .properties-filter-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .properties-filter-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--properties-ink);
    }

    .properties-filter-intro {
        margin: 0.35rem 0 0;
        color: var(--properties-muted);
        font-size: 0.92rem;
    }

    .properties-filter-section + .properties-filter-section {
        margin-top: 1.1rem;
        padding-top: 1.1rem;
        border-top: 1px solid #e2e8f0;
    }

    .properties-filter-label {
        font-size: 0.74rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #73839a;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .filter-checklist {
        display: grid;
        gap: 0.5rem;
    }

    .filter-checklist--compact {
        max-height: 13.5rem;
        overflow: auto;
        padding-right: 0.35rem;
    }

    .filter-checklist label {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: #334155;
        font-size: 0.97rem;
        padding: 0.55rem 0.7rem;
        border-radius: 16px;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .filter-checklist label:hover {
        background: rgba(248, 250, 252, 0.95);
        transform: translateX(2px);
    }

    .properties-filter-card .form-control,
    .properties-filter-card .form-select,
    .properties-toolbar .form-select {
        border-radius: 16px;
        border-color: #d8dee8;
        min-height: 2.8rem;
        box-shadow: none;
    }

    .properties-filter-card .form-control:focus,
    .properties-filter-card .form-select:focus,
    .properties-toolbar .form-select:focus {
        border-color: rgba(184, 138, 59, 0.6);
        box-shadow: 0 0 0 0.2rem rgba(184, 138, 59, 0.14);
    }

    .properties-filter-actions {
        display: grid;
        gap: 0.7rem;
        margin-top: 1.35rem;
    }

    .properties-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.82);
        min-height: 22rem;
        display: grid;
        place-items: center;
    }

    .property-teaser-card {
        overflow: hidden;
        border-radius: 28px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(226, 232, 240, 0.95);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }

    .property-teaser-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 26px 52px rgba(15, 23, 42, 0.12);
        border-color: rgba(184, 138, 59, 0.28);
    }

    .property-teaser-media {
        position: relative;
        display: block;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #e5e7eb;
    }

    .property-teaser-media::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0) 45%, rgba(15, 23, 42, 0.18) 100%);
        pointer-events: none;
    }

    .property-teaser-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .property-teaser-card:hover .property-teaser-media img {
        transform: scale(1.03);
    }

    .property-teaser-favorite {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 2;
    }

    .property-teaser-favorite .btn {
        width: 2.7rem;
        height: 2.7rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.9);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    }

    .property-teaser-body {
        padding: 1.2rem 1.2rem 1.15rem;
    }

    .property-teaser-location {
        color: #8b6a33;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
    }

    .property-teaser-body h3 a {
        color: var(--properties-ink);
        text-decoration: none;
    }

    .property-teaser-price {
        white-space: nowrap;
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        background: var(--properties-sand);
        color: #6e4f1d;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .property-teaser-summary {
        color: #5f6b7a;
        font-size: 0.94rem;
        line-height: 1.6;
        min-height: 3rem;
    }

    .property-teaser-meta {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.7rem;
        margin-top: 1rem;
    }

    .property-teaser-meta span {
        display: grid;
        gap: 0.15rem;
        padding: 0.75rem 0.8rem;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #445161;
        font-size: 0.88rem;
        text-align: center;
    }

    .property-teaser-meta strong {
        color: var(--properties-ink);
        font-size: 0.98rem;
        font-weight: 700;
    }

    .properties-toolbar-copy {
        display: grid;
        gap: 0.2rem;
    }

    .properties-toolbar-copy strong {
        color: var(--properties-ink);
        font-size: 1rem;
    }

    .properties-toolbar-copy span {
        color: var(--properties-muted);
        font-size: 0.92rem;
    }

    .properties-toolbar form {
        min-width: min(100%, 19rem);
    }

    .properties-toolbar label {
        font-size: 0.85rem;
        color: var(--properties-muted);
        font-weight: 600;
    }

    .properties-results-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .properties-results-caption {
        color: var(--properties-muted);
        font-size: 0.94rem;
    }

    @media (max-width: 991.98px) {
        .properties-hero {
            padding: 1.5rem;
        }

        .properties-hero-actions {
            justify-content: flex-start;
        }

        .property-list-grid {
            grid-template-columns: 1fr;
        }

        .properties-filter-card {
            position: static;
        }

        .properties-results-head,
        .properties-toolbar {
            display: grid !important;
        }
    }

    @media (max-width: 575.98px) {
        .properties-title {
            max-width: none;
        }

        .property-teaser-meta {
            grid-template-columns: 1fr;
        }

        .filter-checklist--compact {
            max-height: 11rem;
        }
    }
</style>
@endsection

@section('content')
    <section class="properties-index-shell py-5">
        <div class="container">
            <div class="properties-hero">
                <div class="row g-4 align-items-end">
                    <div class="col-lg-8">
                        <span class="properties-kicker">{{ __('ui.nav.properties') }}</span>
                        <h1 class="properties-title">{{ __('ui.properties.page_title') }}</h1>
                        <p class="properties-intro">{{ __('ui.properties.intro') }}</p>
                    </div>

                    <div class="col-lg-4">
                        <div class="properties-hero-actions">
                            <span class="properties-chip">
                                {{ trans_choice('ui.properties.results_count', $properties->total(), ['count' => $properties->total()]) }}
                            </span>
                            <a href="{{ route('guest.properties.favorites') }}" class="btn btn-outline-dark">
                                {{ __('ui.common.view_favorites') }}
                                @if (($favoritePropertiesCount ?? 0) > 0)
                                    <span class="badge rounded-pill text-bg-dark ms-2">{{ $favoritePropertiesCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            <div class="row g-4 align-items-start">
                <div class="col-lg-4 col-xl-3">
                    <form method="GET" action="{{ route('guest.properties.index') }}" class="properties-filter-card">
                        <div class="properties-filter-head">
                            <div>
                                <p class="properties-filter-title">Filtrado</p>
                                <p class="properties-filter-intro">Afinamos el catálogo para que encuentres algo útil sin pelearte con el listado.</p>
                            </div>
                        </div>

                        <div class="properties-filter-section pt-0 mt-0 border-0">
                            <label class="properties-filter-label" for="property-search">{{ __('ui.properties.filters.search') }}</label>
                            <input
                                id="property-search"
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ $search }}"
                                placeholder="{{ __('ui.properties.filters.search_placeholder') }}"
                            >
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.type') }}</div>
                            <div class="filter-checklist">
                                @foreach ($propertyTypes as $type)
                                    <label>
                                        <input type="checkbox" name="tipo[]" value="{{ $type }}" class="form-check-input" {{ in_array($type, $tipos ?? [], true) ? 'checked' : '' }}>
                                        <span>{{ __('ui.property_types.' . $type) !== 'ui.property_types.' . $type ? __('ui.property_types.' . $type) : ucfirst($type) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.zone') }}</div>
                            <div class="filter-checklist filter-checklist--compact">
                                @foreach ($availableZones as $zone)
                                    <label>
                                        <input type="checkbox" name="zona[]" value="{{ $zone->id }}" class="form-check-input" {{ in_array((string) $zone->id, array_map('strval', $zonas ?? []), true) ? 'checked' : '' }}>
                                        <span>{{ $zone->translatedName() }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.town') }}</div>
                            <div class="filter-checklist filter-checklist--compact">
                                @foreach ($availableLocations as $availableLocation)
                                    <label>
                                        <input type="checkbox" name="location[]" value="{{ $availableLocation }}" class="form-check-input" {{ in_array($availableLocation, $locations ?? [], true) ? 'checked' : '' }}>
                                        <span>{{ $availableLocation }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.price_range') }}</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="precio_min" class="form-control" value="{{ $precioMin }}" placeholder="{{ __('ui.properties.filters.min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="precio_max" class="form-control" value="{{ $precioMax }}" placeholder="{{ __('ui.properties.filters.max_price') }}">
                                </div>
                            </div>
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.minimum_area') }}</div>
                            <input type="number" name="metros" class="form-control" value="{{ is_array($metros) ? '' : $metros }}" placeholder="m2">
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.others') }}</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="habitaciones" class="form-label small">{{ __('ui.properties.filters.bedrooms') }}</label>
                                    <select class="form-select" id="habitaciones" name="habitaciones">
                                        <option value="">{{ __('ui.properties.filters.select') }}</option>
                                        @foreach ([1, 2, 3, 4, 5] as $bedroomOption)
                                            <option value="{{ $bedroomOption }}" {{ (string) $habitaciones === (string) $bedroomOption ? 'selected' : '' }}>{{ $bedroomOption }}+</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="banos" class="form-label small">{{ __('ui.properties.filters.bathrooms') }}</label>
                                    <select class="form-select" id="banos" name="banos">
                                        <option value="">{{ __('ui.properties.filters.select') }}</option>
                                        @foreach ([1, 2, 3, 4] as $bathroomOption)
                                            <option value="{{ $bathroomOption }}" {{ (string) $banos === (string) $bathroomOption ? 'selected' : '' }}>{{ $bathroomOption }}+</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="properties-filter-section">
                            <div class="properties-filter-label">{{ __('ui.properties.filters.features') }}</div>
                            <div class="filter-checklist">
                                <label>
                                    <input type="checkbox" class="form-check-input" name="tiene_solar" value="1" {{ request()->boolean('tiene_solar') ? 'checked' : '' }}>
                                    <span>{{ __('ui.properties.filters.solar') }}</span>
                                </label>
                                <label>
                                    <input type="checkbox" class="form-check-input" name="tiene_patio" value="1" {{ request()->boolean('tiene_patio') ? 'checked' : '' }}>
                                    <span>{{ __('ui.properties.filters.patio') }}</span>
                                </label>
                                <label>
                                    <input type="checkbox" class="form-check-input" name="features[]" value="piscina" {{ in_array('piscina', $features ?? [], true) ? 'checked' : '' }}>
                                    <span>{{ __('ui.properties.filters.pool') }}</span>
                                </label>
                                <label>
                                    <input type="checkbox" class="form-check-input" name="features[]" value="terraza" {{ in_array('terraza', $features ?? [], true) ? 'checked' : '' }}>
                                    <span>{{ __('ui.properties.filters.terrace') }}</span>
                                </label>
                                <label>
                                    <input type="checkbox" class="form-check-input" name="features[]" value="jardin" {{ in_array('jardin', $features ?? [], true) ? 'checked' : '' }}>
                                    <span>{{ __('ui.properties.filters.garden') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="properties-filter-actions">
                            <button type="submit" class="btn btn-dark">{{ __('ui.common.apply_filters') }}</button>
                            <a href="{{ route('guest.properties.index') }}" class="btn btn-outline-secondary">{{ __('ui.common.reset_filters') }}</a>
                        </div>
                    </form>
                </div>

                <div class="col-lg-8 col-xl-9">
                    <div class="properties-results-head">
                        <div class="properties-results-caption">
                            {{ trans_choice('ui.properties.results_count', $properties->total(), ['count' => $properties->total()]) }}
                        </div>
                    </div>

                    <div class="properties-toolbar d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
                        <div class="properties-toolbar-copy">
                            <strong>{{ __('ui.properties.sort.title') }}</strong>
                            <span>{{ __('ui.properties.sort.subtitle') }}</span>
                        </div>

                        <form method="GET" action="{{ route('guest.properties.index') }}" class="d-flex align-items-center gap-2">
                            @foreach (request()->except('sort', 'page') as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $nestedValue)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $nestedValue }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <label for="sort" class="small text-muted mb-0">{{ __('ui.properties.sort.label') }}</label>
                            <select id="sort" name="sort" class="form-select" onchange="this.form.submit()">
                                @foreach ($sortOptions as $sortValue => $sortLabel)
                                    <option value="{{ $sortValue }}" {{ $sort === $sortValue ? 'selected' : '' }}>{{ $sortLabel }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="property-list-grid">
                        @forelse($properties as $property)
                            <div>
                                @include('properties._property-card', ['property' => $property])
                            </div>
                        @empty
                            <div class="properties-empty text-center py-5 px-4">
                                <div>
                                    <p class="mb-3 fs-5">{{ __('ui.properties.empty') }}</p>
                                    <a href="{{ route('guest.properties.index') }}" class="btn btn-outline-dark">{{ __('ui.common.reset_filters') }}</a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $properties->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
