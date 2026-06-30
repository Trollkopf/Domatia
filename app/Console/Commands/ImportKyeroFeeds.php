<?php

namespace App\Console\Commands;

use App\Models\KyeroFeed;
use App\Services\KyeroFeedService;
use Illuminate\Console\Command;
use Throwable;

class ImportKyeroFeeds extends Command
{
    protected $signature = 'kyero:import-feeds {--feed= : Ejecutar solamente una fuente concreta}';

    protected $description = 'Descarga y procesa las fuentes Kyero automáticas activas';

    public function handle(KyeroFeedService $feedService): int
    {
        $query = KyeroFeed::query()->where('is_active', true)->orderBy('id');

        if ($this->option('feed')) {
            $query->whereKey((int) $this->option('feed'));
        }

        $feeds = $query->get();

        if ($feeds->isEmpty()) {
            $this->info('No hay fuentes Kyero activas para procesar.');

            return self::SUCCESS;
        }

        $failed = 0;

        foreach ($feeds as $feed) {
            $this->info("Procesando {$feed->name}...");

            try {
                $run = $feedService->processSynchronously($feed);
                $this->info("Importación {$run->status}: {$run->properties_seen} propiedades leídas.");
            } catch (Throwable $exception) {
                $failed++;
                $this->error("{$feed->name}: {$exception->getMessage()}");
            }
        }

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
