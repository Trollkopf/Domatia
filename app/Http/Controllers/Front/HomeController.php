<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Zona;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Property::query()
            ->where('is_featured', true)
            ->where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        $latestProperties = Property::query()
            ->where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        $homePropertyTypes = Property::query()
            ->where('status', 'published')
            ->whereNotNull('tipo')
            ->where('tipo', '!=', '')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        $homeZones = Zona::query()
            ->whereHas('publishedProperties')
            ->withCount('publishedProperties')
            ->orderBy('nombre')
            ->get();

        return view('welcome', compact('featured', 'latestProperties', 'homePropertyTypes', 'homeZones'));
    }
}
