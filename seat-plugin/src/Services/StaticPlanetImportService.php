<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Services;

use DrNightmare\SeatPiManager\Models\ImportRun;
use DrNightmare\SeatPiManager\Models\StaticPlanet;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class StaticPlanetImportService
{
    public function import(bool $force = false): array
    {
        if (! $force && StaticPlanet::query()->exists()) {
            return [
                'status' => 'skipped',
                'records_processed' => StaticPlanet::query()->count(),
                'message' => 'Static planets already exist. Use --force to rebuild the dataset.',
            ];
        }

        if (! function_exists('bzdecompress')) {
            throw new RuntimeException('The PHP bz2 extension is required to import static planets.');
        }

        $run = ImportRun::query()->create([
            'import_type' => 'static_planets',
            'status' => 'running',
            'started_at' => now(),
            'notes' => 'Downloading and parsing Fuzzwork mapDenormalize.',
        ]);

        try {
            $compressed = Http::timeout(180)
                ->retry(2, 1000)
                ->get((string) config('seat-pi-manager.sde.fuzzwork_denormalize_url'))
                ->throw()
                ->body();

            $decompressed = bzdecompress($compressed);

            if (! is_string($decompressed)) {
                throw new RuntimeException('Unable to decompress Fuzzwork mapDenormalize payload.');
            }

            $rows = $this->parseRows($decompressed);

            StaticPlanet::query()->truncate();

            foreach (array_chunk($rows, 1000) as $chunk) {
                StaticPlanet::query()->insert($chunk);
            }

            $count = count($rows);

            $run->update([
                'status' => 'success',
                'records_processed' => $count,
                'finished_at' => now(),
                'notes' => 'Static planets imported successfully.',
            ]);

            return [
                'status' => 'success',
                'records_processed' => $count,
                'message' => 'Static planets imported successfully.',
            ];
        } catch (Throwable $e) {
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'notes' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function getSummary(): array
    {
        $last_run = ImportRun::query()
            ->where('import_type', 'static_planets')
            ->latest('id')
            ->first();

        return [
            'planet_count' => StaticPlanet::query()->count(),
            'last_run_status' => $last_run?->status,
            'last_run_finished_at' => $last_run?->finished_at,
            'last_run_records' => $last_run?->records_processed ?? 0,
            'last_run_notes' => $last_run?->notes,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function parseRows(string $sql): array
    {
        $pattern = "/\\((\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(NULL|\\d+),(-?[\\d.eE+\\-]+),(-?[\\d.eE+\\-]+),(-?[\\d.eE+\\-]+),(-?[\\d.eE+\\-]+),'([^']+)',(-?[\\d.eE+\\-]+|NULL),(NULL|\\d+),(NULL|\\d+)\\)/";

        $rows = [];
        $now = now();

        foreach (preg_split('/\R/', $sql) as $line) {
            if (! str_contains($line, 'INSERT INTO')) {
                continue;
            }

            if (! preg_match_all($pattern, $line, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $match) {
                $celestial_index = $match[14];
                $orbit_index = $match[15];

                if ($celestial_index === 'NULL' || $orbit_index !== 'NULL') {
                    continue;
                }

                $rows[] = [
                    'planet_id' => (int) $match[1],
                    'system_id' => (int) $match[4],
                    'planet_name' => $match[12],
                    'planet_number' => (string) $celestial_index,
                    'radius' => (int) floor((float) $match[11]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $rows;
    }
}
