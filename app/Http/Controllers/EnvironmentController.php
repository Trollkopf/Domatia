<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;
use Str;

class EnvironmentController extends Controller
{
    public function index()
    {
        $zonas = Zona::select('id', 'nombre', 'nombre_en', 'nombre_fr', 'nombre_de', 'nombre_ru', 'imagen_principal', 'slug')
            ->with(['representativePublishedProperty', 'representativeProperty'])
            ->orderBy('nombre')
            ->get();

        return view('environment.index', compact('zonas'));
    }

    public function show($slug)
    {
        $zona = Zona::whereRaw('LOWER(slug) = ?', [Str::slug($slug)])
            ->with([
                'secciones',
                'representativePublishedProperty',
                'representativeProperty',
                'publishedProperties' => fn ($query) => $query->latest(),
            ])
            ->firstOrFail();

        return view('environment.show', compact('zona'));
    }


}
