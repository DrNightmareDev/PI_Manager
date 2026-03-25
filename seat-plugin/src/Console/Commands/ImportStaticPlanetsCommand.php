<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Console\Commands;

use DrNightmare\SeatPiManager\Services\StaticPlanetImportService;
use Illuminate\Console\Command;
use Throwable;

class ImportStaticPlanetsCommand extends Command
{
    protected $signature = 'seat-pi-manager:import-static-planets {--force : Rebuild the dataset even if rows already exist}';

    protected $description = 'Download and import static planet metadata for the SeAT PI Manager plugin.';

    public function handle(StaticPlanetImportService $service): int
    {
        try {
            $result = $service->import((bool) $this->option('force'));

            $status = (string) ($result['status'] ?? 'unknown');
            $records = (int) ($result['records_processed'] ?? 0);
            $message = (string) ($result['message'] ?? '');

            if ($status === 'skipped') {
                $this->warn($message);
                $this->line(sprintf('Existing static planets: %d', $records));

                return self::SUCCESS;
            }

            $this->info($message);
            $this->line(sprintf('Imported static planets: %d', $records));

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
