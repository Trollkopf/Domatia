<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Property;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.index', $this->buildReportPayload($request));
    }

    public function export(Request $request)
    {
        $report = $this->buildReportPayload($request);
        $filename = 'informes-domatia-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Seccion', 'Etiqueta', 'Valor'], ';');

            foreach ($this->buildExportRows($report) as $row) {
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function buildReportPayload(Request $request): array
    {
        $filters = [
            'from' => $request->input('from', now()->subMonths(5)->startOfMonth()->toDateString()),
            'to' => $request->input('to', now()->toDateString()),
            'zona_id' => $request->input('zona_id'),
            'tipo' => $request->input('tipo'),
        ];

        $fromDate = Carbon::parse($filters['from'])->startOfDay();
        $toDate = Carbon::parse($filters['to'])->endOfDay();

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
            $filters['from'] = $fromDate->toDateString();
            $filters['to'] = $toDate->toDateString();
        }

        $propertyScope = Property::query();

        if ($filters['zona_id']) {
            $propertyScope->where('zona_id', $filters['zona_id']);
        }

        if ($filters['tipo']) {
            $propertyScope->where('tipo', $filters['tipo']);
        }

        $propertyIds = (clone $propertyScope)->pluck('id');

        $leadScope = Contacto::query()
            ->whereBetween('created_at', [$fromDate, $toDate]);

        if ($filters['zona_id'] || $filters['tipo']) {
            $leadScope->whereIn('property_id', $propertyIds);
        }

        $overview = [
            'published_properties' => (clone $propertyScope)->where('status', 'published')->count(),
            'draft_properties' => (clone $propertyScope)->where('status', 'draft')->count(),
            'featured_properties' => (clone $propertyScope)->where('is_featured', true)->count(),
            'total_leads' => (clone $leadScope)->count(),
            'pending_leads' => (clone $leadScope)->where('status', 'pendiente')->count(),
            'closed_leads' => (clone $leadScope)->where('status', 'cerrado')->count(),
            'avg_price' => (float) (clone $propertyScope)->where('status', 'published')->avg('price'),
        ];

        $conversionRate = $overview['total_leads'] > 0
            ? round(($overview['closed_leads'] / $overview['total_leads']) * 100, 1)
            : 0;

        $leadStatusBreakdown = (clone $leadScope)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $inventoryByStatus = (clone $propertyScope)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $inventoryByType = (clone $propertyScope)
            ->selectRaw('COALESCE(tipo, "Sin tipo") as tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->orderByDesc('total')
            ->get();

        $monthlyLeads = $this->buildMonthlyLeads(
            (clone $leadScope)->get(['created_at']),
            $fromDate,
            $toDate
        );

        $topPropertiesByLeads = Property::query()
            ->withCount([
                'contactos' => fn ($query) => $query->whereBetween('created_at', [$fromDate, $toDate]),
            ])
            ->with('zona')
            ->when($filters['zona_id'], fn ($query) => $query->where('zona_id', $filters['zona_id']))
            ->when($filters['tipo'], fn ($query) => $query->where('tipo', $filters['tipo']))
            ->orderByDesc('contactos_count')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $topZones = Zona::query()
            ->leftJoin('properties', 'properties.zona_id', '=', 'zonas.id')
            ->leftJoin('contactos', function ($join) use ($fromDate, $toDate) {
                $join->on('contactos.property_id', '=', 'properties.id')
                    ->whereBetween('contactos.created_at', [$fromDate, $toDate]);
            })
            ->when($filters['zona_id'], fn ($query) => $query->where('zonas.id', $filters['zona_id']))
            ->when($filters['tipo'], fn ($query) => $query->where('properties.tipo', $filters['tipo']))
            ->select(
                'zonas.id',
                'zonas.nombre',
                'zonas.slug',
                DB::raw('COUNT(DISTINCT properties.id) as properties_count'),
                DB::raw("COUNT(DISTINCT CASE WHEN properties.status = 'published' THEN properties.id END) as published_properties_count"),
                DB::raw('COUNT(contactos.id) as leads_count')
            )
            ->groupBy('zonas.id', 'zonas.nombre', 'zonas.slug')
            ->orderByDesc('leads_count')
            ->orderByDesc('published_properties_count')
            ->take(5)
            ->get();

        $propertyFilterParams = array_filter([
            'zona_id' => $filters['zona_id'],
            'tipo' => $filters['tipo'],
        ], fn ($value) => filled($value));

        $qualityChecks = [
            [
                'label' => 'Propiedades sin imagen principal',
                'count' => (clone $propertyScope)->where(function ($query) {
                    $query->whereNull('thumbnail')->orWhere('thumbnail', '');
                })->count(),
                'url' => route('admin.properties.index', array_merge($propertyFilterParams, ['missing_thumbnail' => 1])),
                'action' => 'Revisar fichas',
            ],
            [
                'label' => 'Leads abiertos sin siguiente accion',
                'count' => (clone $leadScope)->where('status', '!=', 'cerrado')->whereNull('next_action_at')->count(),
                'url' => route('admin.contactos.index'),
                'action' => 'Ordenar seguimiento',
            ],
            [
                'label' => 'Zonas sin imagen principal',
                'count' => Zona::query()
                    ->when($filters['zona_id'], fn ($query) => $query->where('id', $filters['zona_id']))
                    ->where(function ($query) {
                        $query->whereNull('imagen_principal')->orWhere('imagen_principal', '');
                    })->count(),
                'url' => route('admin.zonas.index'),
                'action' => 'Completar zonas',
            ],
        ];

        $zonas = Zona::orderBy('nombre')->get(['id', 'nombre']);
        $tipos = Property::query()
            ->whereNotNull('tipo')
            ->where('tipo', '!=', '')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return compact(
            'overview',
            'conversionRate',
            'leadStatusBreakdown',
            'inventoryByStatus',
            'inventoryByType',
            'monthlyLeads',
            'topPropertiesByLeads',
            'topZones',
            'qualityChecks',
            'filters',
            'zonas',
            'tipos'
        );
    }

    protected function buildExportRows(array $report): array
    {
        $rows = [
            ['Filtros', 'Desde', $report['filters']['from']],
            ['Filtros', 'Hasta', $report['filters']['to']],
            ['Filtros', 'Zona', $report['zonas']->firstWhere('id', $report['filters']['zona_id'])?->nombre ?: 'Todas'],
            ['Filtros', 'Tipo', $report['filters']['tipo'] ?: 'Todos'],
            ['Resumen', 'Propiedades publicadas', $report['overview']['published_properties']],
            ['Resumen', 'Propiedades en borrador', $report['overview']['draft_properties']],
            ['Resumen', 'Propiedades destacadas', $report['overview']['featured_properties']],
            ['Resumen', 'Leads totales', $report['overview']['total_leads']],
            ['Resumen', 'Leads pendientes', $report['overview']['pending_leads']],
            ['Resumen', 'Leads cerrados', $report['overview']['closed_leads']],
            ['Resumen', 'Conversion cerrada', $report['conversionRate'] . '%'],
            ['Resumen', 'Precio medio publicado', number_format($report['overview']['avg_price'], 2, '.', '')],
        ];

        foreach ($report['leadStatusBreakdown'] as $item) {
            $rows[] = ['Leads por estado', ucfirst($item->status), $item->total];
        }

        foreach ($report['inventoryByStatus'] as $item) {
            $rows[] = ['Inventario por estado', $item->status, $item->total];
        }

        foreach ($report['inventoryByType'] as $item) {
            $rows[] = ['Inventario por tipo', $item->tipo, $item->total];
        }

        foreach ($report['monthlyLeads'] as $item) {
            $rows[] = ['Leads mensuales', $item->period, $item->total];
        }

        foreach ($report['topPropertiesByLeads'] as $property) {
            $rows[] = ['Top propiedades', $property->title, $property->contactos_count];
        }

        foreach ($report['topZones'] as $zona) {
            $rows[] = ['Top zonas', $zona->nombre, $zona->leads_count];
        }

        foreach ($report['qualityChecks'] as $check) {
            $rows[] = ['Chequeos de calidad', $check['label'], $check['count']];
        }

        return $rows;
    }

    protected function buildMonthlyLeads(Collection $leads, Carbon $fromDate, Carbon $toDate): Collection
    {
        $grouped = $leads
            ->groupBy(fn ($lead) => Carbon::parse($lead->created_at)->format('Y-m'))
            ->map(fn ($items, $period) => (object) [
                'period' => $period,
                'total' => $items->count(),
            ]);

        $cursor = $fromDate->copy()->startOfMonth();
        $months = collect();

        while ($cursor->lte($toDate)) {
            $period = $cursor->format('Y-m');
            $months->push($grouped->get($period, (object) [
                'period' => $period,
                'total' => 0,
            ]));

            $cursor->addMonth();
        }

        return $months;
    }
}
