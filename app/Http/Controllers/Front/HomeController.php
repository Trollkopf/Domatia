<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Property;

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

        return view('welcome', compact('featured', 'latestProperties'));
    }
}
