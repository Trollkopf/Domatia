<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KyeroFeed;
use App\Models\PropertyImportRun;
use App\Services\KyeroFeedService;
use App\Services\KyeroImportService;
use Illuminate\Http\Request;
use Throwable;

class KyeroImportController extends Controller
{
    public function __construct(
        protected KyeroImportService $kyeroImportService,
        protected KyeroFeedService $kyeroFeedService
    )
    {
    }

    public function index()
    {
        $latestRuns = PropertyImportRun::query()
            ->with(['user', 'kyeroFeed'])
            ->latest()
            ->take(10)
            ->get();

        $activeRun = null;
        $runId = request()->integer('run');

        if ($runId) {
            $activeRun = PropertyImportRun::query()->find($runId);
        }

        $feeds = KyeroFeed::query()->with('lastRun')->orderBy('name')->get();

        return view('admin.kyero.index', compact('latestRuns', 'activeRun', 'feeds'));
    }

    public function storeFeed(Request $request)
    {
        $validated = $request->validate($this->feedRules());

        KyeroFeed::create(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('admin.kyero.index')->with('success', 'Fuente Kyero guardada.');
    }

    public function updateFeed(Request $request, KyeroFeed $feed)
    {
        $validated = $request->validate($this->feedRules());
        $feed->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('admin.kyero.index')->with('success', 'Fuente Kyero actualizada.');
    }

    public function destroyFeed(KyeroFeed $feed)
    {
        $feed->delete();

        return redirect()->route('admin.kyero.index')->with('success', 'Fuente Kyero eliminada.');
    }

    public function runFeed(Request $request, KyeroFeed $feed)
    {
        try {
            $run = $this->kyeroFeedService->prepare($feed, $request->user());
            $backgroundStarted = $this->kyeroImportService->startBackgroundProcessing($run, 5);
        } catch (Throwable $exception) {
            return redirect()->route('admin.kyero.index')
                ->withErrors(['feed_url' => $exception->getMessage()]);
        }

        return redirect()->route('admin.kyero.index', ['run' => $run->id])
            ->with('success', $backgroundStarted
                ? 'Feed descargado. La importación se está procesando en segundo plano.'
                : 'Feed descargado y preparado. Puedes procesarlo manualmente desde esta pantalla.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'xml_file' => 'nullable|file|mimes:xml,txt|max:10240',
            'xml_content' => 'nullable|string',
            'max_images_per_property' => 'nullable|integer|min:1|max:30',
        ]);

        $xml = $request->filled('xml_content')
            ? trim((string) $request->input('xml_content'))
            : null;

        $inputName = 'Pegado manual';

        if ($request->hasFile('xml_file')) {
            $xml = $request->file('xml_file')->get();
            $inputName = $request->file('xml_file')->getClientOriginalName();
        }

        if (! $xml) {
            return back()->withErrors([
                'xml_file' => 'Sube un XML o pega el contenido del feed para poder importarlo.',
            ])->withInput();
        }

        try {
            $run = $this->kyeroImportService->prepareImport(
                $xml,
                $request->user(),
                $inputName,
                (int) ($request->input('max_images_per_property', 12))
            );
            $backgroundStarted = $this->kyeroImportService->startBackgroundProcessing($run, 1);
        } catch (Throwable $exception) {
            return back()->withErrors([
                'xml_file' => $exception->getMessage(),
            ])->withInput();
        }

        return redirect()->route('admin.kyero.index', ['run' => $run->id])
            ->with('success', $backgroundStarted
                ? 'Importacion preparada. Se esta procesando en segundo plano.'
                : 'Importacion preparada. Si no avanza sola, puedes dejar esta pantalla abierta o procesar tandas manualmente.');
    }

    public function process(PropertyImportRun $run)
    {
        try {
            $run = $this->kyeroImportService->processNextChunk($run, 1);
            $this->kyeroFeedService->syncFeedStatus($run);
        } catch (Throwable $exception) {
            $run->refresh();
            $this->kyeroFeedService->syncFeedStatus($run);

            return response()->json([
                'ok' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

        return response()->json($this->buildRunPayload($run));
    }

    public function show(PropertyImportRun $run)
    {
        return response()->json($this->buildRunPayload($run));
    }

    protected function buildRunPayload(PropertyImportRun $run): array
    {
        $run->refresh();
        $progress = $run->total_properties > 0
            ? (int) floor(($run->properties_seen / $run->total_properties) * 100)
            : 0;

        return [
            'ok' => true,
            'run' => [
                'id' => $run->id,
                'status' => $run->status,
                'notes' => $run->notes,
                'total_properties' => $run->total_properties,
                'properties_seen' => $run->properties_seen,
                'properties_created' => $run->properties_created,
                'properties_updated' => $run->properties_updated,
                'properties_skipped' => $run->properties_skipped,
                'images_downloaded' => $run->images_downloaded,
                'max_images_per_property' => $run->max_images_per_property,
                'progress' => min(100, $progress),
            ],
        ];
    }

    protected function feedRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url:http,https|max:2048',
            'is_active' => 'nullable|boolean',
            'max_images_per_property' => 'required|integer|min:1|max:30',
        ];
    }
}
