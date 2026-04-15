@extends('layouts.guest')

@section('title', __('ui.properties.page_title'))

@section('style')
<link href="{{ asset('css/properties.css') }}" rel="stylesheet">
<style>
    .properties-index-shell {
        background:
            radial-gradient(circle at top left, rgba(212, 165, 45, 0.08), transparent 25%),
            linear-gradient(180deg, #f8fafc 0%, #ffffff 40%);
    }

    .property-list-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.5rem;
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

    .property-teaser-favorite {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .property-teaser-favorite .btn {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
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
        .property-list-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <div class="bg-light py-4 border-bottom">
        <form method="GET" action="{{ route('guest.properties.index') }}" class="container">
            <div class="row g-2 align-items-center justify-content-center">
                <div class="col-md-2 dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                        {{ __('ui.properties.filters.type') }}
                    </button>
                    <ul class="dropdown-menu p-2" style="width: 100%;">
                        <li><label><input type="checkbox" name="tipo[]" value="piso" class="form-check-input me-2"> {{ __('ui.property_types.piso') }}</label></li>
                        <li><label><input type="checkbox" name="tipo[]" value="casa" class="form-check-input me-2"> {{ __('ui.property_types.casa') }}</label></li>
                        <li><label><input type="checkbox" name="tipo[]" value="villa" class="form-check-input me-2"> {{ __('ui.property_types.villa') }}</label></li>
                    </ul>
                </div>

                <div class="col-md-2 dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                        {{ __('ui.properties.filters.minimum_area') }}
                    </button>
                    <ul class="dropdown-menu p-2" style="width: 100%;">
                        <li><label><input type="radio" name="metros" value="50" class="form-check-input me-2"> 50 m2</label></li>
                        <li><label><input type="radio" name="metros" value="100" class="form-check-input me-2"> 100 m2</label></li>
                        <li><label><input type="radio" name="metros" value="200" class="form-check-input me-2"> 200 m2</label></li>
                    </ul>
                </div>

                <div class="col-md-2 dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                        {{ __('ui.properties.filters.town') }}
                    </button>
                    <ul class="dropdown-menu p-2" style="width: 100%;">
                        <li><label><input type="checkbox" name="location[]" value="Madrid" class="form-check-input me-2"> Madrid</label></li>
                        <li><label><input type="checkbox" name="location[]" value="Barcelona" class="form-check-input me-2"> Barcelona</label></li>
                        <li><label><input type="checkbox" name="location[]" value="Alicante" class="form-check-input me-2"> Alicante</label></li>
                    </ul>
                </div>

                <div class="col-md-2 dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                        {{ __('ui.properties.filters.max_price') }}
                    </button>
                    <ul class="dropdown-menu p-2" style="width: 100%;">
                        <li><label><input type="radio" name="precio_max" value="500000" class="form-check-input me-2"> {{ __('ui.properties.filters.up_to', ['price' => '500.000']) }}</label></li>
                        <li><label><input type="radio" name="precio_max" value="1000000" class="form-check-input me-2"> {{ __('ui.properties.filters.up_to', ['price' => '1.000.000']) }}</label></li>
                        <li><label><input type="radio" name="precio_max" value="1500000" class="form-check-input me-2"> {{ __('ui.properties.filters.up_to', ['price' => '1.500.000']) }}</label></li>
                    </ul>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="button" class="btn btn-outline-dark w-100" data-bs-toggle="modal" data-bs-target="#filtersModal">
                        <i class="bi bi-sliders"></i> {{ __('ui.common.more_filters') }}
                    </button>
                    <button type="submit" class="btn btn-main w-100 d-flex justify-content-center align-items-center">
                        <i class="bi bi-search me-2"></i> {{ __('ui.common.show') }}
                    </button>
                </div>
            </div>

            <div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filtersModalLabel">{{ __('ui.common.more_filters') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="mb-3">{{ __('ui.properties.filters.features') }}</h6>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="features[]" value="piscina" id="piscina">
                                        <label class="form-check-label" for="piscina">{{ __('ui.properties.filters.pool') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="features[]" value="terraza" id="terraza">
                                        <label class="form-check-label" for="terraza">{{ __('ui.properties.filters.terrace') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="features[]" value="jardin" id="jardin">
                                        <label class="form-check-label" for="jardin">{{ __('ui.properties.filters.garden') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="mb-3">{{ __('ui.properties.filters.others') }}</h6>
                                    <div class="mb-3">
                                        <label for="habitaciones" class="form-label">{{ __('ui.properties.filters.bedrooms') }}</label>
                                        <select class="form-select" id="habitaciones" name="habitaciones">
                                            <option value="">{{ __('ui.properties.filters.select') }}</option>
                                            <option value="1">1+</option>
                                            <option value="2">2+</option>
                                            <option value="3">3+</option>
                                            <option value="4">4+</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bano" class="form-label">{{ __('ui.properties.filters.bathrooms') }}</label>
                                        <select class="form-select" id="bano" name="banos">
                                            <option value="">{{ __('ui.properties.filters.select') }}</option>
                                            <option value="1">1+</option>
                                            <option value="2">2+</option>
                                            <option value="3">3+</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-main w-100">{{ __('ui.common.apply_filters') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <section class="properties-index-shell pt-5 pb-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="mb-2 fw-light">{{ __('ui.properties.page_title') }}</h1>
                    <p class="text-muted mb-0">{{ __('ui.properties.intro') }}</p>
                </div>

                <a href="{{ route('guest.properties.favorites') }}" class="btn btn-outline-dark">
                    {{ __('ui.common.view_favorites') }}
                    @if (($favoritePropertiesCount ?? 0) > 0)
                        <span class="badge rounded-pill text-bg-dark ms-2">{{ $favoritePropertiesCount }}</span>
                    @endif
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
            @endif

            <div class="property-list-grid">
                @forelse($properties as $property)
                    <div>
                        @include('properties._property-card', ['property' => $property])
                    </div>
                @empty
                    <div class="text-center py-5">
                        <p>{{ __('ui.properties.empty') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $properties->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>
@endsection
