<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = array_filter([
            'search' => $request->input('search', $request->input('q', $request->input('location'))),
            'tipo' => $request->filled('type') ? [$request->input('type')] : $request->input('tipo'),
            'precio_min' => $request->input('precio_min', $request->input('min_price')),
            'precio_max' => $request->input('precio_max', $request->input('max_price')),
            'sort' => $request->input('sort'),
        ], fn ($value) => $value !== null && $value !== '' && $value !== []);

        return redirect()->route('guest.properties.index', $query);
    }
}
