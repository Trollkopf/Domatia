<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::query();

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

        $properties = $properties->with('zona')->paginate(12)->withQueryString();

        return view('properties.index', compact('properties'));
    }

    public function show($slug)
    {
        $property = Property::where('slug', $slug)->with('images')->firstOrFail();

        return view('properties.show', compact('property'));
    }
}
