<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    protected const FAVORITES_COOKIE = 'favorite_properties';
    protected const FAVORITES_COOKIE_MINUTES = 43200;

    public function index(Request $request)
    {
        $properties = Property::query()->where('status', 'published');

        $tipos = array_filter((array) $request->input('tipo', $request->input('types', [])));
        if ($tipos !== []) {
            $properties->whereIn('tipo', $tipos);
        }

        $zonas = array_filter((array) $request->input('zona', []));
        if ($zonas !== []) {
            $properties->whereIn('zona_id', $zonas);
        }

        $locations = array_filter((array) $request->input('location', []));
        if ($locations !== []) {
            $properties->whereIn('location', $locations);
        }

        $precioMin = $request->input('precio_min', $request->input('min_price'));
        if (filled($precioMin)) {
            $properties->where('price', '>=', $precioMin);
        }

        $precioMax = $request->input('precio_max', $request->input('max_price'));
        if (filled($precioMax)) {
            $properties->where('price', '<=', $precioMax);
        }

        $habitaciones = $request->input('habitaciones', $request->input('bedrooms'));
        if (filled($habitaciones)) {
            $properties->where('bedrooms', '>=', $habitaciones);
        }

        $banos = $request->input('banos', $request->input('bathrooms'));
        if (filled($banos)) {
            $properties->where('bathrooms', '>=', $banos);
        }

        $metros = $request->input('metros');
        if (is_array($metros)) {
            $metros = min(array_map('floatval', array_filter($metros)));
        }
        if (filled($metros)) {
            $properties->where('area', '>=', $metros);
        }

        if ($request->boolean('tiene_solar')) {
            $properties->where('tiene_solar', true);
        }

        if ($request->boolean('tiene_patio')) {
            $properties->where('tiene_patio', true);
        }

        if ($request->boolean('tiene_piscina')) {
            $properties->where('tiene_piscina', true);
        }

        $features = array_filter((array) $request->input('features', []));
        if (in_array('piscina', $features, true)) {
            $properties->where('tiene_piscina', true);
        }
        if (in_array('jardin', $features, true) || in_array('terraza', $features, true)) {
            $properties->where('tiene_patio', true);
        }

        $properties = $properties->with('zona')->latest()->paginate(12)->withQueryString();

        return view('properties.index', compact('properties'));
    }

    public function show(Request $request, $slug)
    {
        $property = Property::where('slug', $slug)
            ->where('status', 'published')
            ->with(['images', 'zona'])
            ->firstOrFail();

        $galleryImages = $property->images
            ->pluck('path')
            ->prepend($property->thumbnail)
            ->filter()
            ->unique()
            ->values();

        $relatedProperties = Property::query()
            ->where('status', 'published')
            ->whereKeyNot($property->id)
            ->when($property->zona_id, function ($query) use ($property) {
                $query->where('zona_id', $property->zona_id);
            }, function ($query) use ($property) {
                $query->where('tipo', $property->tipo);
            })
            ->with('zona')
            ->latest()
            ->take(3)
            ->get();

        if ($relatedProperties->count() < 3) {
            $fallbackProperties = Property::query()
                ->where('status', 'published')
                ->whereKeyNot($property->id)
                ->whereNotIn('id', $relatedProperties->pluck('id'))
                ->with('zona')
                ->latest()
                ->take(3 - $relatedProperties->count())
                ->get();

            $relatedProperties = $relatedProperties->concat($fallbackProperties);
        }

        $favoriteSlugs = $this->getFavoriteSlugs($request);

        return view('properties.show', compact('property', 'galleryImages', 'relatedProperties', 'favoriteSlugs'));
    }

    public function favorites(Request $request)
    {
        $favoriteSlugs = $this->getFavoriteSlugs($request);

        $properties = Property::query()
            ->where('status', 'published')
            ->whereIn('slug', $favoriteSlugs)
            ->with('zona')
            ->get()
            ->sortBy(function (Property $property) use ($favoriteSlugs) {
                return array_search($property->slug, $favoriteSlugs, true);
            })
            ->values();

        return view('properties.favorites', compact('properties', 'favoriteSlugs'));
    }

    public function toggleFavorite(Request $request, $slug)
    {
        $property = Property::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        $favoriteSlugs = $this->getFavoriteSlugs($request);
        $isFavorite = in_array($property->slug, $favoriteSlugs, true);

        if ($isFavorite) {
            $favoriteSlugs = array_values(array_filter($favoriteSlugs, fn ($favoriteSlug) => $favoriteSlug !== $property->slug));
            $message = __('ui.properties.favorite_removed');
        } else {
            array_unshift($favoriteSlugs, $property->slug);
            $favoriteSlugs = array_values(array_unique($favoriteSlugs));
            $message = __('ui.properties.favorite_added');
        }

        $cookieValue = implode(',', $favoriteSlugs);
        $cookie = cookie(
            self::FAVORITES_COOKIE,
            $cookieValue,
            self::FAVORITES_COOKIE_MINUTES
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_favorite' => ! $isFavorite,
                'favorites_count' => count($favoriteSlugs),
                'favorite_slugs' => $favoriteSlugs,
                'message' => $message,
                'property_slug' => $property->slug,
            ])->cookie($cookie);
        }

        return redirect($request->input('redirect_to', route('guest.property.show', $property->slug)))
            ->with('success', $message)
            ->cookie($cookie);
    }

    protected function getFavoriteSlugs(Request $request): array
    {
        $rawValue = (string) $request->cookie(self::FAVORITES_COOKIE, '');

        $decoded = json_decode($rawValue, true);

        if (is_array($decoded)) {
            $values = $decoded;
        } else {
            $values = $rawValue === '' ? [] : explode(',', $rawValue);
        }

        return collect($values)
            ->filter(fn ($slug) => is_string($slug) && $slug !== '')
            ->unique()
            ->values()
            ->all();
    }
}
