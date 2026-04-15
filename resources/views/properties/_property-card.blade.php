@php
    $isFavorite = in_array($property->slug, $favoritePropertySlugs ?? [], true);
@endphp

<article class="property-teaser-card h-100">
    <div class="position-relative">
        <a href="{{ route('guest.property.show', $property->slug) }}" class="property-teaser-media">
            <img
                src="{{ $property->thumbnail ? asset('storage/' . $property->thumbnail) : asset('images/our-company.jpg') }}"
                alt="{{ $property->title }}"
            >
        </a>

        <form action="{{ route('guest.property.favorite', $property->slug) }}" method="POST" class="property-teaser-favorite" data-favorite-toggle-form data-property-slug="{{ $property->slug }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->full() }}">
            <button type="submit" class="btn btn-sm {{ $isFavorite ? 'btn-dark' : 'btn-light' }}" data-favorite-toggle-button aria-pressed="{{ $isFavorite ? 'true' : 'false' }}">
                <i class="fa-{{ $isFavorite ? 'solid' : 'regular' }} fa-heart" data-favorite-toggle-icon></i>
            </button>
        </form>
    </div>

    <div class="property-teaser-body">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
            <div>
                <p class="property-teaser-location mb-1">
                    {{ $property->zona->nombre ?? ($property->location ?: 'Ubicacion por confirmar') }}
                </p>
                <h3 class="h5 mb-0">
                    <a href="{{ route('guest.property.show', $property->slug) }}" class="text-dark">
                        {{ $property->title }}
                    </a>
                </h3>
            </div>

            <div class="property-teaser-price">{{ number_format($property->price, 0, ',', '.') }} EUR</div>
        </div>

        <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit($property->description, 110) }}</p>

        <div class="property-teaser-meta">
            <span>{{ $property->bedrooms ?: '-' }} hab</span>
            <span>{{ $property->bathrooms ?: '-' }} banos</span>
            <span>{{ $property->area ? number_format($property->area, 0, ',', '.') . ' m2' : 'm2 por confirmar' }}</span>
        </div>
    </div>
</article>
