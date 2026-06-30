<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Propietario;
use App\Models\Zona;
use App\Support\PropertyFeatureSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->applyPropertyFilters(Property::query()->with('zona'), $request->all());
        $matchingDraftCount = (clone $query)->where('status', 'draft')->count();

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

        return view('admin.properties.index', compact('properties', 'zonas', 'tipos', 'statuses', 'matchingDraftCount'));
    }

    public function create()
    {
        $zonas = Zona::query()->orderBy('nombre')->get();
        $selectedPropietario = old('propietario_id')
            ? Propietario::query()->find(old('propietario_id'))
            : null;

        return view('admin.properties.create', compact('zonas', 'selectedPropietario'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->propertyRules());

        $publicationState = $this->resolvePublicationState($request);
        $property = Property::create($this->buildPropertyPayload($request, $validated, $publicationState));

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
        $zonas = Zona::query()->orderBy('nombre')->get();
        $property = Property::with('images')->findOrFail($id);
        $selectedPropietarioId = old('propietario_id', $property->propietario_id);
        $selectedPropietario = $selectedPropietarioId
            ? Propietario::query()->find($selectedPropietarioId)
            : null;

        return view('admin.properties.edit', compact('property', 'zonas', 'selectedPropietario'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate($this->propertyRules());

        $publicationState = $this->resolvePublicationState($request, $property);
        $property->update($this->buildPropertyPayload($request, $validated, $publicationState));

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

        if ($request->expectsJson()) {
            $statusLabels = [
                'draft' => 'Borrador',
                'published' => 'Publicada',
                'reserved' => 'Reservada',
                'sold' => 'Vendida',
                'hidden' => 'Oculta',
            ];

            return response()->json([
                'success' => true,
                'message' => 'Propiedad actualizada.',
                'property_id' => $property->id,
                'status' => $property->status,
                'status_label' => $statusLabels[$property->status] ?? ucfirst($property->status),
                'is_featured' => $property->is_featured,
            ]);
        }

        return redirect()->route('admin.properties.index', $request->except(['status', 'toggle_featured']))
            ->with('success', 'Propiedad actualizada.');
    }

    public function bulkPublish(Request $request)
    {
        $validated = $request->validate([
            'scope' => 'required|in:selected,filtered',
            'property_ids' => 'required_if:scope,selected|array|max:500',
            'property_ids.*' => 'integer|exists:properties,id',
            'filters' => 'nullable|array',
            'filters.search' => 'nullable|string|max:255',
            'filters.zona_id' => 'nullable|integer',
            'filters.tipo' => 'nullable|string|max:100',
            'filters.status' => 'nullable|in:draft,published,reserved,sold,hidden',
            'filters.featured' => 'nullable|in:0,1',
            'filters.price_min' => 'nullable|numeric',
            'filters.price_max' => 'nullable|numeric',
            'filters.missing_thumbnail' => 'nullable|in:0,1',
        ]);

        $query = Property::query();

        if ($validated['scope'] === 'selected') {
            $query->whereIn('id', $validated['property_ids'] ?? []);
        } else {
            $query = $this->applyPropertyFilters($query, $validated['filters'] ?? []);
        }

        $propertyIds = $query->where('status', 'draft')->pluck('id');
        $updatedCount = Property::query()->whereIn('id', $propertyIds)->update(['status' => 'published']);
        $message = $updatedCount === 1
            ? 'Se ha publicado 1 propiedad.'
            : "Se han publicado {$updatedCount} propiedades.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $updatedCount,
                'property_ids' => $propertyIds->values(),
            ]);
        }

        return redirect()->route('admin.properties.index', $request->input('filters', []))
            ->with('success', $message);
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Propiedad eliminada exitosamente.');
    }

    private function applyPropertyFilters($query, array $filters)
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('ref', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['zona_id'])) {
            $query->where('zona_id', $filters['zona_id']);
        }

        if (! empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (array_key_exists('featured', $filters) && $filters['featured'] !== '') {
            $query->where('is_featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if ($filters['price_min'] ?? null) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if ($filters['price_max'] ?? null) {
            $query->where('price', '<=', $filters['price_max']);
        }

        if (filter_var($filters['missing_thumbnail'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $query->where(function ($subQuery) {
                $subQuery->whereNull('thumbnail')->orWhere('thumbnail', '');
            });
        }

        return $query;
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

    private function propertyRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'title_fr' => 'nullable|string|max:255',
            'title_de' => 'nullable|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'location_fr' => 'nullable|string|max:255',
            'location_de' => 'nullable|string|max:255',
            'location_ru' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'location_detail' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'price' => 'nullable|numeric',
            'currency' => 'nullable|string|max:8',
            'price_freq' => 'nullable|string|max:32',
            'tipo' => 'nullable|string|max:100',
            'zona_id' => 'nullable|exists:zonas,id',
            'propietario_id' => 'nullable|exists:propietarios,id',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_de' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_extra' => 'nullable|array',
            'description_extra.*' => 'nullable|string',
            'source_notes' => 'nullable|string',
            'banos' => 'nullable|integer',
            'habitaciones' => 'nullable|integer',
            'metros' => 'nullable|numeric',
            'tiene_solar' => 'nullable|boolean',
            'metros_solar' => 'nullable|numeric',
            'tiene_patio' => 'nullable|boolean',
            'destacada' => 'nullable|boolean',
            'tiene_piscina' => 'nullable|boolean',
            'has_air_conditioning' => 'nullable|boolean',
            'has_garage' => 'nullable|boolean',
            'has_lift' => 'nullable|boolean',
            'has_garden' => 'nullable|boolean',
            'has_terrace' => 'nullable|boolean',
            'has_sea_views' => 'nullable|boolean',
            'has_parking' => 'nullable|boolean',
            'is_furnished' => 'nullable|boolean',
            'has_storage_room' => 'nullable|boolean',
            'has_solarium' => 'nullable|boolean',
            'part_ownership' => 'nullable|boolean',
            'leasehold' => 'nullable|boolean',
            'new_build' => 'nullable|boolean',
            'energy_consumption' => 'nullable|string|max:32',
            'energy_emissions' => 'nullable|string|max:32',
            'video_url' => 'nullable|url|max:2048',
            'virtual_tour_url' => 'nullable|url|max:2048',
            'features_text' => 'nullable|string',
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
        ];
    }

    private function buildPropertyPayload(Request $request, array $validated, array $publicationState): array
    {
        $features = $this->normalizeFeatureInput($request->input('features_text'));
        $derivedFlags = PropertyFeatureSupport::inferFlags($features);
        $extraDescriptions = collect((array) ($validated['description_extra'] ?? []))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->all();

        return [
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'title_fr' => $validated['title_fr'] ?? null,
            'title_de' => $validated['title_de'] ?? null,
            'title_ru' => $validated['title_ru'] ?? null,
            'location' => $validated['location'] ?? null,
            'location_en' => $validated['location_en'] ?? null,
            'location_fr' => $validated['location_fr'] ?? null,
            'location_de' => $validated['location_de'] ?? null,
            'location_ru' => $validated['location_ru'] ?? null,
            'town' => $validated['town'] ?? null,
            'province' => $validated['province'] ?? null,
            'country' => $validated['country'] ?? null,
            'location_detail' => $validated['location_detail'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'price' => $validated['price'] ?? null,
            'currency' => $validated['currency'] ?? null,
            'price_freq' => $validated['price_freq'] ?? null,
            'tipo' => $validated['tipo'] ?? null,
            'zona_id' => $validated['zona_id'] ?? null,
            'propietario_id' => $validated['propietario_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_en' => $validated['description_en'] ?? null,
            'description_fr' => $validated['description_fr'] ?? null,
            'description_de' => $validated['description_de'] ?? null,
            'description_ru' => $validated['description_ru'] ?? null,
            'description_extra' => $extraDescriptions !== [] ? $extraDescriptions : null,
            'source_notes' => $validated['source_notes'] ?? null,
            'bathrooms' => $validated['banos'] ?? null,
            'bedrooms' => $validated['habitaciones'] ?? null,
            'area' => $validated['metros'] ?? null,
            'tiene_solar' => $request->boolean('tiene_solar') || ($derivedFlags['tiene_solar'] ?? false),
            'metros_solar' => $validated['metros_solar'] ?? null,
            'tiene_patio' => $request->boolean('tiene_patio') || ($derivedFlags['tiene_patio'] ?? false),
            'is_featured' => $publicationState['is_featured'],
            'tiene_piscina' => $request->boolean('tiene_piscina') || ($derivedFlags['tiene_piscina'] ?? false),
            'has_air_conditioning' => $request->boolean('has_air_conditioning') || ($derivedFlags['has_air_conditioning'] ?? false),
            'has_garage' => $request->boolean('has_garage') || ($derivedFlags['has_garage'] ?? false),
            'has_lift' => $request->boolean('has_lift') || ($derivedFlags['has_lift'] ?? false),
            'has_garden' => $request->boolean('has_garden') || ($derivedFlags['has_garden'] ?? false),
            'has_terrace' => $request->boolean('has_terrace') || ($derivedFlags['has_terrace'] ?? false),
            'has_sea_views' => $request->boolean('has_sea_views') || ($derivedFlags['has_sea_views'] ?? false),
            'has_parking' => $request->boolean('has_parking') || ($derivedFlags['has_parking'] ?? false),
            'is_furnished' => $request->boolean('is_furnished') || ($derivedFlags['is_furnished'] ?? false),
            'has_storage_room' => $request->boolean('has_storage_room') || ($derivedFlags['has_storage_room'] ?? false),
            'has_solarium' => $request->boolean('has_solarium') || ($derivedFlags['has_solarium'] ?? false),
            'part_ownership' => $request->boolean('part_ownership'),
            'leasehold' => $request->boolean('leasehold'),
            'new_build' => $request->boolean('new_build'),
            'energy_consumption' => $validated['energy_consumption'] ?? null,
            'energy_emissions' => $validated['energy_emissions'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'virtual_tour_url' => $validated['virtual_tour_url'] ?? null,
            'features_json' => $features !== [] ? $features : null,
            'status' => $publicationState['status'],
            'quick_summary_1' => $validated['quick_summary_1'] ?? null,
            'quick_summary_2' => $validated['quick_summary_2'] ?? null,
            'quick_summary_3' => $validated['quick_summary_3'] ?? null,
            'quick_summary_1_en' => $validated['quick_summary_1_en'] ?? null,
            'quick_summary_1_fr' => $validated['quick_summary_1_fr'] ?? null,
            'quick_summary_1_de' => $validated['quick_summary_1_de'] ?? null,
            'quick_summary_1_ru' => $validated['quick_summary_1_ru'] ?? null,
            'quick_summary_2_en' => $validated['quick_summary_2_en'] ?? null,
            'quick_summary_2_fr' => $validated['quick_summary_2_fr'] ?? null,
            'quick_summary_2_de' => $validated['quick_summary_2_de'] ?? null,
            'quick_summary_2_ru' => $validated['quick_summary_2_ru'] ?? null,
            'quick_summary_3_en' => $validated['quick_summary_3_en'] ?? null,
            'quick_summary_3_fr' => $validated['quick_summary_3_fr'] ?? null,
            'quick_summary_3_de' => $validated['quick_summary_3_de'] ?? null,
            'quick_summary_3_ru' => $validated['quick_summary_3_ru'] ?? null,
        ];
    }

    private function normalizeFeatureInput(?string $value): array
    {
        $items = preg_split('/[\r\n,;]+/', (string) $value) ?: [];

        return PropertyFeatureSupport::normalizeList($items);
    }
}
