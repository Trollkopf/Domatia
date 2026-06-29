<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyImportRun;
use App\Services\KyeroImportService;
use Illuminate\Http\Request;
use Throwable;

class KyeroImportController extends Controller
{
    public function __construct(protected KyeroImportService $kyeroImportService)
    {
    }

    public function index()
    {
        $latestRuns = PropertyImportRun::query()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $activeRun = null;
        $runId = request()->integer('run');

        if ($runId) {
            $activeRun = PropertyImportRun::query()->find($runId);
        }

        return view('admin.kyero.index', compact('latestRuns', 'activeRun'));
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
        } catch (Throwable $exception) {
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
}
