<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;
use Str;

class EnvironmentController extends Controller
{
    public function index()
    {
        $zonas = Zona::select('nombre', 'imagen_principal', 'slug')->get();

        return view('environment.index', compact('zonas'));
    }

    public function show($slug)
    {
        $zona = Zona::whereRaw('LOWER(slug) = ?', [Str::slug($slug)])
        ->with(['secciones', 'properties'])
        ->firstOrFail();

    return view('environment.show', compact('zona'));
    }


}
