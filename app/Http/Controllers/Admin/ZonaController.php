<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use App\Models\ZonaSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ZonaController extends Controller
{
    public function index()
    {
        $zonas = Zona::with([
            'secciones',
            'representativePublishedProperty',
            'representativeProperty',
        ])->orderBy('nombre')->get();

        return view('admin.zonas.index', compact('zonas'));
    }

    public function create()
    {
        return view('admin.zonas.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateZona($request);

        $zona = new Zona();
        $zona->nombre = $validated['nombre'];
        $zona->nombre_en = $validated['nombre_en'] ?? null;
        $zona->nombre_fr = $validated['nombre_fr'] ?? null;
        $zona->nombre_de = $validated['nombre_de'] ?? null;
        $zona->nombre_ru = $validated['nombre_ru'] ?? null;

        if ($request->hasFile('imagen_principal')) {
            $zona->imagen_principal = $request->file('imagen_principal')->store('zonas', 'public');
        }

        $zona->save();

        $this->syncSections($zona, $validated['secciones'] ?? [], $request, false);

        return redirect()->route('admin.zonas.index')->with('success', 'Zona creada correctamente.');
    }

    public function update(Request $request, Zona $zona)
    {
        $validated = $this->validateZona($request);

        $zona->nombre = $validated['nombre'];
        $zona->nombre_en = $validated['nombre_en'] ?? null;
        $zona->nombre_fr = $validated['nombre_fr'] ?? null;
        $zona->nombre_de = $validated['nombre_de'] ?? null;
        $zona->nombre_ru = $validated['nombre_ru'] ?? null;

        if ($request->hasFile('imagen_principal')) {
            if ($zona->imagen_principal) {
                Storage::disk('public')->delete($zona->imagen_principal);
            }

            $zona->imagen_principal = $request->file('imagen_principal')->store('zonas', 'public');
        }

        $zona->save();

        $this->syncSections($zona, $validated['secciones'] ?? [], $request, true);

        return redirect()->route('admin.zonas.index')->with('success', 'Zona actualizada correctamente.');
    }

    public function edit(Zona $zona)
    {
        return view('admin.zonas.edit', compact('zona'));
    }

    public function destroy(Zona $zona)
    {
        if ($zona->imagen_principal) {
            Storage::disk('public')->delete($zona->imagen_principal);
        }

        foreach ($zona->secciones as $seccion) {
            if ($seccion->imagen) {
                Storage::disk('public')->delete($seccion->imagen);
            }
        }

        $zona->delete();

        return back()->with('success', 'Zona eliminada.');
    }

    protected function validateZona(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_en' => 'nullable|string|max:255',
            'nombre_fr' => 'nullable|string|max:255',
            'nombre_de' => 'nullable|string|max:255',
            'nombre_ru' => 'nullable|string|max:255',
            'imagen_principal' => 'nullable|image|max:5120',
            'secciones' => 'nullable|array',
            'secciones.*.id' => 'nullable|integer',
            'secciones.*.titulo' => 'nullable|string|max:255',
            'secciones.*.titulo_en' => 'nullable|string|max:255',
            'secciones.*.titulo_fr' => 'nullable|string|max:255',
            'secciones.*.titulo_de' => 'nullable|string|max:255',
            'secciones.*.titulo_ru' => 'nullable|string|max:255',
            'secciones.*.descripcion' => 'nullable|string',
            'secciones.*.descripcion_en' => 'nullable|string',
            'secciones.*.descripcion_fr' => 'nullable|string',
            'secciones.*.descripcion_de' => 'nullable|string',
            'secciones.*.descripcion_ru' => 'nullable|string',
            'secciones.*.imagen' => 'nullable|image|max:5120',
        ]);
    }

    protected function syncSections(Zona $zona, array $sections, Request $request, bool $isUpdate): void
    {
        $keptIds = [];

        foreach ($sections as $index => $sectionData) {
            $hasContent = filled($sectionData['titulo'] ?? null)
                || filled($sectionData['descripcion'] ?? null)
                || $request->hasFile("secciones.$index.imagen");

            if (! $hasContent) {
                continue;
            }

            $section = null;
            if ($isUpdate && ! empty($sectionData['id'])) {
                $section = $zona->secciones()->whereKey($sectionData['id'])->first();
            }

            if (! $section) {
                $section = new ZonaSection();
                $section->zona_id = $zona->id;
            }

            $section->titulo = $sectionData['titulo'] ?? '';
            $section->titulo_en = $sectionData['titulo_en'] ?? null;
            $section->titulo_fr = $sectionData['titulo_fr'] ?? null;
            $section->titulo_de = $sectionData['titulo_de'] ?? null;
            $section->titulo_ru = $sectionData['titulo_ru'] ?? null;
            $section->descripcion = $sectionData['descripcion'] ?? null;
            $section->descripcion_en = $sectionData['descripcion_en'] ?? null;
            $section->descripcion_fr = $sectionData['descripcion_fr'] ?? null;
            $section->descripcion_de = $sectionData['descripcion_de'] ?? null;
            $section->descripcion_ru = $sectionData['descripcion_ru'] ?? null;

            if ($request->hasFile("secciones.$index.imagen")) {
                if ($section->imagen) {
                    Storage::disk('public')->delete($section->imagen);
                }

                $section->imagen = $request->file("secciones.$index.imagen")->store('zonas/secciones', 'public');
            }

            $section->save();
            $keptIds[] = $section->id;
        }

        if ($isUpdate) {
            $zona->secciones()
                ->whereNotIn('id', $keptIds)
                ->get()
                ->each(function (ZonaSection $section): void {
                    if ($section->imagen) {
                        Storage::disk('public')->delete($section->imagen);
                    }

                    $section->delete();
                });
        }
    }
}
