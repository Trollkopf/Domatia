<?php

namespace App\Services;

use App\Models\KyeroFeed;
use App\Models\PropertyImportRun;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class KyeroFeedService
{
    public function __construct(protected KyeroImportService $kyeroImportService)
    {
    }

    public function prepare(KyeroFeed $feed, ?User $user = null): PropertyImportRun
    {
        $feed->update([
            'last_status' => 'downloading',
            'last_error' => null,
            'last_run_at' => now(),
        ]);

        try {
            $response = Http::accept('application/xml')
                ->withUserAgent(config('app.name', 'Domatia') . '/KyeroImporter')
                ->connectTimeout(20)
                ->timeout(120)
                ->retry(2, 1000)
                ->get($feed->url);

            if (! $response->successful()) {
                throw new RuntimeException("El feed ha respondido con HTTP {$response->status()}.");
            }

            $xml = $response->body();

            if ($xml === '') {
                throw new RuntimeException('El feed remoto está vacío.');
            }

            if (strlen($xml) > 50 * 1024 * 1024) {
                throw new RuntimeException('El feed supera el límite de 50 MB.');
            }

            $run = $this->kyeroImportService->prepareImport(
                $xml,
                $user,
                $feed->name . ' · ' . $feed->url,
                $feed->max_images_per_property
            );

            $run->update(['kyero_feed_id' => $feed->id]);
            $feed->update([
                'last_import_run_id' => $run->id,
                'last_status' => 'queued',
            ]);

            return $run->fresh();
        } catch (Throwable $exception) {
            $feed->update([
                'last_status' => 'failed',
                'last_error' => Str::limit($exception->getMessage(), 2000),
            ]);

            throw $exception;
        }
    }

    public function processSynchronously(KyeroFeed $feed, ?User $user = null, int $chunkSize = 5): PropertyImportRun
    {
        $run = $this->prepare($feed, $user);

        try {
            while (! in_array($run->status, ['completed', 'failed'], true)) {
                $run = $this->kyeroImportService->processNextChunk($run, $chunkSize);
            }
        } finally {
            $this->syncFeedStatus($run->fresh());
        }

        return $run->fresh();
    }

    public function syncFeedStatus(PropertyImportRun $run): void
    {
        $feed = $run->kyeroFeed;

        if (! $feed) {
            return;
        }

        $feed->update([
            'last_import_run_id' => $run->id,
            'last_status' => $run->status,
            'last_error' => $run->status === 'failed' ? $run->notes : null,
            'last_success_at' => $run->status === 'completed' ? now() : $feed->last_success_at,
        ]);
    }
}
