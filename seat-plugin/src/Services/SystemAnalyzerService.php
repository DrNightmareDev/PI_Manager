<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Services;

use Illuminate\Support\Facades\DB;

class SystemAnalyzerService
{
    public function __construct(
        private readonly PiCatalogService $catalog,
        private readonly PiRecommendationService $recommendations,
    ) {
    }

    public function getBootstrapSummary(): array
    {
        return [
            'status' => 'active',
            'message' => 'The SeAT-native analyzer is backed by static planet data and PI recommendation logic.',
            'next_step' => 'Search a system to inspect planets, available P0 resources, and feasible PI products.',
        ];
    }

    public function searchSystems(string $query, int $limit = 10): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $q = mb_strtolower($query);

        $rows = DB::table('solar_systems as ss')
            ->leftJoin('regions as r', 'r.region_id', '=', 'ss.region_id')
            ->leftJoin('constellations as c', 'c.constellation_id', '=', 'ss.constellation_id')
            ->select([
                'ss.system_id',
                'ss.name',
                'ss.security',
                'r.name as region_name',
                'c.name as constellation_name',
            ])
            ->where(function ($builder) use ($query, $q) {
                $builder
                    ->whereRaw('LOWER(ss.name) = ?', [$q])
                    ->orWhereRaw('LOWER(ss.name) LIKE ?', [$q . '%'])
                    ->orWhereRaw('LOWER(ss.name) LIKE ?', ['%' . $q . '%']);
            })
            ->orderByRaw('CASE WHEN LOWER(ss.name) = ? THEN 0 WHEN LOWER(ss.name) LIKE ? THEN 1 ELSE 2 END', [$q, $q . '%'])
            ->orderBy('ss.name')
            ->limit($limit)
            ->get();

        return $rows->map(function ($row) {
            return [
                'system_id' => (int) $row->system_id,
                'name' => $row->name,
                'security' => round((float) $row->security, 2),
                'region_name' => $row->region_name,
                'constellation_name' => $row->constellation_name,
            ];
        })->all();
    }

    public function getSystemDetails(?string $query): ?array
    {
        if ($query === null || trim($query) === '') {
            return null;
        }

        $query = trim($query);

        $row = DB::table('solar_systems as ss')
            ->leftJoin('regions as r', 'r.region_id', '=', 'ss.region_id')
            ->leftJoin('constellations as c', 'c.constellation_id', '=', 'ss.constellation_id')
            ->select([
                'ss.system_id',
                'ss.name',
                'ss.security',
                'r.name as region_name',
                'c.name as constellation_name',
            ])
            ->when(
                ctype_digit($query),
                fn ($builder) => $builder->where('ss.system_id', (int) $query),
                fn ($builder) => $builder->whereRaw('LOWER(ss.name) = ?', [mb_strtolower($query)])
            )
            ->first();

        if (! $row) {
            return null;
        }

        $planets = $this->getPlanetsForSystem((int) $row->system_id);

        return [
            'system_id' => (int) $row->system_id,
            'name' => $row->name,
            'security' => round((float) $row->security, 2),
            'region_name' => $row->region_name,
            'constellation_name' => $row->constellation_name,
            'planet_count' => count($planets),
            'planets' => $planets,
            'planet_type_summary' => $this->buildPlanetTypeSummary($planets),
            'recommendations' => $this->recommendations->analyzePlanetTypes(array_map(
                static fn (array $planet): string => (string) ($planet['type_name'] ?? ''),
                array_filter($planets, static fn (array $planet): bool => ! empty($planet['type_name']))
            )),
        ];
    }

    public function analyzeSystemMix(array $queries): array
    {
        $systems = [];
        $allPlanets = [];

        foreach ($queries as $query) {
            $details = $this->getSystemDetails($query);
            if (! $details) {
                continue;
            }
            $systems[] = $details;
            foreach ($details['planets'] as $planet) {
                $allPlanets[] = $planet;
            }
        }

        $planetTypes = array_map(
            static fn (array $planet): string => (string) ($planet['type_name'] ?? ''),
            array_filter($allPlanets, static fn (array $planet): bool => ! empty($planet['type_name']))
        );

        return [
            'systems' => $systems,
            'planet_type_summary' => $this->buildPlanetTypeSummary($allPlanets),
            'planet_count' => count($allPlanets),
            'recommendations' => $this->recommendations->analyzePlanetTypes($planetTypes),
        ];
    }

    public function getPlanetsForSystem(int $systemId): array
    {
        $rows = DB::table('seat_pi_manager_static_planets as spp')
            ->leftJoin('planets as p', 'p.planet_id', '=', 'spp.planet_id')
            ->select([
                'spp.planet_id',
                'spp.planet_name',
                'spp.planet_number',
                'spp.radius',
                'p.type_id',
                'p.celestial_index',
            ])
            ->where('spp.system_id', $systemId)
            ->orderByRaw('COALESCE(p.celestial_index, 999999) ASC')
            ->orderBy('spp.planet_name')
            ->get();

        return $rows->map(function ($row) {
            $radiusMeters = $row->radius !== null ? (int) $row->radius : null;

            return [
                'planet_id' => (int) $row->planet_id,
                'planet_name' => $row->planet_name,
                'planet_number' => $row->planet_number,
                'radius_m' => $radiusMeters,
                'radius_km' => $radiusMeters !== null ? number_format((int) round($radiusMeters / 1000)) : null,
                'type_id' => $row->type_id !== null ? (int) $row->type_id : null,
                'type_name' => $this->planetTypeLabel($row->type_id !== null ? (int) $row->type_id : null),
            ];
        })->all();
    }

    private function planetTypeLabel(?int $typeId): ?string
    {
        if ($typeId === null) {
            return null;
        }

        return match ($typeId) {
            11 => 'Temperate',
            12 => 'Ice',
            13 => 'Gas',
            2014 => 'Oceanic',
            2015 => 'Burned',
            2016 => 'Barren',
            2017 => 'Lava',
            2063 => 'Plasma',
            30889 => 'Shattered',
            73911 => 'Storm',
            default => 'Type ' . $typeId,
        };
    }

    private function buildPlanetTypeSummary(array $planets): array
    {
        $counts = [];
        $colors = $this->catalog->getPlanetTypeColors();
        $resources = $this->catalog->getPlanetResources();

        foreach ($planets as $planet) {
            $typeName = $planet['type_name'] ?? null;
            if (! $typeName) {
                continue;
            }
            $counts[$typeName] = ($counts[$typeName] ?? 0) + 1;
        }

        $summary = [];
        foreach ($counts as $typeName => $count) {
            $summary[] = [
                'type' => $typeName,
                'count' => $count,
                'color' => $colors[$typeName] ?? '#6c757d',
                'resources' => $resources[$typeName] ?? [],
            ];
        }

        usort($summary, static fn (array $a, array $b): int => $b['count'] <=> $a['count']);

        return $summary;
    }
}
