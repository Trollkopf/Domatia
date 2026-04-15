<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Propietario;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query()->with('zona');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('ref', 'like', "%{$search}%");
            });
        }

        if ($request->filled('zona_id')) {
            $query->where('zona_id', $request->input('zona_id'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        if ($request->boolean('missing_thumbnail')) {
            $query->where(function ($subQuery) {
                $subQuery->whereNull('thumbnail')->orWhere('thumbnail', '');
            });
        }

        $properties = $query->latest()->paginate(10)->withQueryString();
        $zonas = Zona::orderBy('nombre')->get(['id', 'nombre']);
        $tipos = Property::query()
            ->whereNotNull('tipo')
            ->where('tipo', '!=', '')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');
        $statuses = collect([
            'draft' => 'Borrador',
            'published' => 'Publicada',
            'reserved' => 'Reservada',
            'sold' => 'Vendida',
            'hidden' => 'Oculta',
        ]);

        return view('admin.properties.index', compact('properties', 'zonas', 'tipos', 'statuses'));
    }

    public function create()
    {
        $zonas = Zona::all();
        $propietarios = Propietario::all();

        return view('admin.properties.create', compact('zonas', 'propietarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'title_de' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'location_en' => 'nullable|string|max:255',
            'location_fr' => 'nullable|string|max:255',
            'location_de' => 'nullable|string|max:255',
            'location_ru' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'tipo' => 'nullable|string|max:100',
            'zona_id' => 'nullable|exists:zonas,id',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_de' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'banos' => 'nullable|integer',
            'habitaciones' => 'nullable|integer',
            'metros' => 'nullable|integer',
            'tiene_solar' => 'nullable|boolean',
            'metros_solar' => 'nullable|integer',
            'tiene_patio' => 'nullable|boolean',
            'destacada' => 'nullable|boolean',
            'tiene_piscina' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published,reserved,sold,hidden',
            'quick_summary_1' => 'nullable|string|max:255',
            'quick_summary_2' => 'nullable|string|max:255',
            'quick_summary_3' => 'nullable|string|max:255',
            'quick_summary_1_en' => 'nullable|string|max:255',
            'quick_summary_1_fr' => 'nullable|string|max:255',
            'quick_summary_1_de' => 'nullable|string|max:255',
            'quick_summary_1_ru' => 'nullable|string|max:255',
            'quick_summary_2_en' => 'nullable|string|max:255',
            'quick_summary_2_fr' => 'nullable|string|max:255',
            'quick_summary_2_de' => 'nullable|string|max:255',
            'quick_summary_2_ru' => 'nullable|string|max:255',
            'quick_summary_3_en' => 'nullable|string|max:255',
            'quick_summary_3_fr' => 'nullable|string|max:255',
            'quick_summary_3_de' => 'nullable|string|max:255',
            'quick_summary_3_ru' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $publicationState = $this->resolvePublicationState($request);

        $property = Property::create([
            'title' => $request->title,
            'title_en' => $request->title_en,
            'title_fr' => $request->title_fr,
            'title_de' => $request->title_de,
            'title_ru' => $request->title_ru,
            'location' => $request->location,
            'location_en' => $request->location_en,
            'location_fr' => $request->location_fr,
            'location_de' => $request->location_de,
            'location_ru' => $request->location_ru,
            'price' => $request->price,
            'tipo' => $request->tipo,
            'zona_id' => $request->zona_id,
            'propietario_id' => $request->propietario_id,
            'description' => $request->description,
            'description_en' => $request->description_en,
            'description_fr' => $request->description_fr,
            'description_de' => $request->description_de,
            'description_ru' => $request->description_ru,
            'bathrooms' => $request->banos,
            'bedrooms' => $request->habitaciones,
            'area' => $request->metros,
            'tiene_solar' => $request->has('tiene_solar'),
            'metros_solar' => $request->metros_solar,
            'tiene_patio' => $request->has('tiene_patio'),
            'is_featured' => $publicationState['is_featured'],
            'tiene_piscina' => $request->has('tiene_piscina'),
            'status' => $publicationState['status'],
            'quick_summary_1' => $request->quick_summary_1,
            'quick_summary_2' => $request->quick_summary_2,
            'quick_summary_3' => $request->quick_summary_3,
            'quick_summary_1_en' => $request->quick_summary_1_en,
            'quick_summary_1_fr' => $request->quick_summary_1_fr,
            'quick_summary_1_de' => $request->quick_summary_1_de,
            'quick_summary_1_ru' => $request->quick_summary_1_ru,
            'quick_summary_2_en' => $request->quick_summary_2_en,
            'quick_summary_2_fr' => $request->quick_summary_2_fr,
            'quick_summary_2_de' => $request->quick_summary_2_de,
            'quick_summary_2_ru' => $request->quick_summary_2_ru,
            'quick_summary_3_en' => $request->quick_summary_3_en,
            'quick_summary_3_fr' => $request->quick_summary_3_fr,
            'quick_summary_3_de' => $request->quick_summary_3_de,
            'quick_summary_3_ru' => $request->quick_summary_3_ru,
        ]);

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $count = 2;
        while (Property::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }
        $property->slug = $slug;
        $property->ref = "R-{$property->id}";

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');

                if ($index === 0) {
                    $property->thumbnail = $path;
                } else {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'url' => "storage/$path",
                        'path' => $path,
                    ]);
                }
            }
        }

        $property->save();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad creada correctamente.');
    }

    public function edit($id)
    {
        $zonas = Zona::all();
        $propietarios = Propietario::all();
        $property = Property::with('images')->findOrFail($id);

        return view('admin.properties.edit', compact('property', 'zonas', 'propietarios'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'title_de' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'location_en' => 'nullable|string|max:255',
            'location_fr' => 'nullable|string|max:255',
            'location_de' => 'nullable|string|max:255',
            'location_ru' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'tipo' => 'nullable|string|max:100',
            'zona_id' => 'nullable|exists:zonas,id',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_de' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'banos' => 'nullable|integer',
            'habitaciones' => 'nullable|integer',
            'metros' => 'nullable|integer',
            'tiene_solar' => 'nullable|boolean',
            'metros_solar' => 'nullable|integer',
            'tiene_patio' => 'nullable|boolean',
            'tiene_piscina' => 'nullable|boolean',
            'destacada' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published,reserved,sold,hidden',
            'quick_summary_1' => 'nullable|string|max:255',
            'quick_summary_2' => 'nullable|string|max:255',
            'quick_summary_3' => 'nullable|string|max:255',
            'quick_summary_1_en' => 'nullable|string|max:255',
            'quick_summary_1_fr' => 'nullable|string|max:255',
            'quick_summary_1_de' => 'nullable|string|max:255',
            'quick_summary_1_ru' => 'nullable|string|max:255',
            'quick_summary_2_en' => 'nullable|string|max:255',
            'quick_summary_2_fr' => 'nullable|string|max:255',
            'quick_summary_2_de' => 'nullable|string|max:255',
            'quick_summary_2_ru' => 'nullable|string|max:255',
            'quick_summary_3_en' => 'nullable|string|max:255',
            'quick_summary_3_fr' => 'nullable|string|max:255',
            'quick_summary_3_de' => 'nullable|string|max:255',
            'quick_summary_3_ru' => 'nullable|string|max:255',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $publicationState = $this->resolvePublicationState($request, $property);

        $property->update([
            'title' => $request->title,
            'title_en' => $request->title_en,
            'title_fr' => $request->title_fr,
            'title_de' => $request->title_de,
            'title_ru' => $request->title_ru,
            'location' => $request->location,
            'location_en' => $request->location_en,
            'location_fr' => $request->location_fr,
            'location_de' => $request->location_de,
            'location_ru' => $request->location_ru,
            'price' => $request->price,
            'tipo' => $request->tipo,
            'zona_id' => $request->zona_id,
            'propietario_id' => $request->propietario_id,
            'description' => $request->description,
            'description_en' => $request->description_en,
            'description_fr' => $request->description_fr,
            'description_de' => $request->description_de,
            'description_ru' => $request->description_ru,
            'bathrooms' => $request->banos,
            'bedrooms' => $request->habitaciones,
            'area' => $request->metros,
            'tiene_solar' => $request->has('tiene_solar'),
            'metros_solar' => $request->metros_solar,
            'tiene_patio' => $request->has('tiene_patio'),
            'is_featured' => $publicationState['is_featured'],
            'tiene_piscina' => $request->has('tiene_piscina'),
            'status' => $publicationState['status'],
            'quick_summary_1' => $request->quick_summary_1,
            'quick_summary_2' => $request->quick_summary_2,
            'quick_summary_3' => $request->quick_summary_3,
            'quick_summary_1_en' => $request->quick_summary_1_en,
            'quick_summary_1_fr' => $request->quick_summary_1_fr,
            'quick_summary_1_de' => $request->quick_summary_1_de,
            'quick_summary_1_ru' => $request->quick_summary_1_ru,
            'quick_summary_2_en' => $request->quick_summary_2_en,
            'quick_summary_2_fr' => $request->quick_summary_2_fr,
            'quick_summary_2_de' => $request->quick_summary_2_de,
            'quick_summary_2_ru' => $request->quick_summary_2_ru,
            'quick_summary_3_en' => $request->quick_summary_3_en,
            'quick_summary_3_fr' => $request->quick_summary_3_fr,
            'quick_summary_3_de' => $request->quick_summary_3_de,
            'quick_summary_3_ru' => $request->quick_summary_3_ru,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');

                if ($index === 0 && ! $property->thumbnail) {
                    $property->thumbnail = $path;
                } else {
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'url' => "storage/$path",
                        'path' => $path,
                    ]);
                }
            }

            $property->save();
        }

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad actualizada correctamente.');
    }

    public function quickUpdate(Request $request, Property $property)
    {
        if (! $request->user()?->canPublishProperties()) {
            return redirect()->route('admin.properties.index', $request->except(['status', 'toggle_featured']))
                ->with('error', 'No tienes permisos para publicar o destacar propiedades.');
        }

        $validated = $request->validate([
            'status' => 'nullable|in:draft,published,reserved,sold,hidden',
            'toggle_featured' => 'nullable|boolean',
        ]);

        if (array_key_exists('status', $validated) && $validated['status']) {
            $property->status = $validated['status'];
        }

        if ($request->boolean('toggle_featured')) {
            $property->is_featured = ! $property->is_featured;
        }

        $property->save();

        return redirect()->route('admin.properties.index', $request->except(['status', 'toggle_featured']))
            ->with('success', 'Propiedad actualizada.');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad eliminada exitosamente.');
    }

    private function resolvePublicationState(Request $request, ?Property $property = null): array
    {
        if ($request->user()?->canPublishProperties()) {
            return [
                'is_featured' => $request->has('destacada'),
                'status' => $request->input('status', $property?->status ?? 'draft'),
            ];
        }

        return [
            'is_featured' => (bool) ($property?->is_featured ?? false),
            'status' => $property?->status ?? 'draft',
        ];
    }
}
