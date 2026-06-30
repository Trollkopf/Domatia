<?php

namespace App\Console\Commands;

use App\Models\PropertyImportRun;
use App\Services\KyeroImportService;
use App\Services\KyeroFeedService;
use Illuminate\Console\Command;

class ProcessKyeroImportRun extends Command
{
    protected $signature = 'kyero:process-run {run : ID de la importacion} {--chunk=1 : Propiedades por iteracion}';

    protected $description = 'Procesa una importacion de Kyero pendiente o en curso';

    public function handle(KyeroImportService $kyeroImportService, KyeroFeedService $kyeroFeedService): int
    {
        $run = PropertyImportRun::query()->find($this->argument('run'));

        if (! $run) {
            $this->error('No se encontro la importacion solicitada.');

            return self::FAILURE;
        }

        $chunk = max(1, (int) $this->option('chunk'));

        try {
            while (! in_array($run->status, ['completed', 'failed'], true)) {
                $run = $kyeroImportService->processNextChunk($run, $chunk);
            }
        } finally {
            $run->refresh();
            $kyeroFeedService->syncFeedStatus($run);
        }

        $this->info('Importación finalizada con estado: ' . $run->status);

        return $run->status === 'completed'
            ? self::SUCCESS
            : self::FAILURE;
    }
}
