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
        $zonas = Zona::with('secciones')->orderBy('nombre')->get();

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
            'imagen_principal' => 'nullable|image|max:5120',
            'secciones' => 'nullable|array',
            'secciones.*.id' => 'nullable|integer',
            'secciones.*.titulo' => 'nullable|string|max:255',
            'secciones.*.descripcion' => 'nullable|string',
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
            $section->descripcion = $sectionData['descripcion'] ?? null;

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
