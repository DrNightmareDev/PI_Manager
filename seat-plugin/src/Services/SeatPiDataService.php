<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class SeatPiDataService
{
    public function __construct(
        private readonly PiCatalogService $catalog,
    ) {
    }

    public function getDashboardData(array $filters = []): array
    {
        $rows = $this->baseColonyRows();
        $characters = [];
        $systemNames = [];
        $planetRows = [];
        $now = CarbonImmutable::now('UTC');

        foreach ($rows as $row) {
            $planetType = $this->normalizePlanetType($row->planet_type_name ?? $row->planet_type ?? null);
            $extractorResources = $this->splitCsv($row->extractor_products ?? '');
            $contentNames = $this->splitCsv($row->content_names ?? '');
            $allNames = array_values(array_unique(array_merge($extractorResources, $contentNames)));
            $highestTier = $this->highestTierForNames($allNames);
            $nextExpiry = $row->next_expiry ? CarbonImmutable::parse((string) $row->next_expiry, 'UTC') : null;
            $status = $this->deriveStatus((int) $row->extractor_count, $nextExpiry, $now);

            $planet = [
                'character_id' => (int) $row->character_id,
                'character_name' => $row->character_name ?: ('Character #' . $row->character_id),
                'corporation_name' => $row->corporation_name,
                'alliance_name' => $row->alliance_name,
                'system_id' => (int) $row->solar_system_id,
                'system_name' => $row->system_name ?: ('System #' . $row->solar_system_id),
                'region_name' => $row->region_name,
                'constellation_name' => $row->constellation_name,
                'planet_id' => (int) $row->planet_id,
                'planet_name' => $row->planet_name ?: ('Planet #' . $row->planet_id),
                'planet_number' => $row->planet_number,
                'planet_type' => $planetType,
                'planet_color' => $this->catalog->getPlanetTypeColors()[$planetType] ?? '#6c757d',
                'upgrade_level' => (int) $row->upgrade_level,
                'num_pins' => (int) $row->num_pins,
                'last_update' => $row->last_update,
                'extractor_count' => (int) $row->extractor_count,
                'factory_count' => (int) $row->factory_count,
                'storage_stacks' => (int) $row->storage_stacks,
                'storage_total_amount' => (int) $row->storage_total_amount,
                'extractor_products' => $extractorResources,
                'content_names' => $contentNames,
                'all_product_names' => $allNames,
                'highest_tier' => $highestTier,
                'next_expiry' => $nextExpiry,
                'next_expiry_human' => $nextExpiry ? $nextExpiry->diffForHumans($now, [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => CarbonImmutable::DIFF_RELATIVE_TO_NOW,
                ]) : null,
                'next_expiry_unix' => $nextExpiry?->timestamp,
                'total_u_per_hour' => round((float) $row->total_u_per_hour, 2),
                'status' => $status,
                'has_single_planet_viable_products' => false,
            ];

            $recommendations = $planetType
                ? $this->recommendPlanetTypes([$planetType])
                : [];
            $planet['single_planet_products'] = array_values(array_filter(
                $recommendations,
                static fn (array $item): bool => ! empty($item['single_planet_viable'])
            ));
            $planet['has_single_planet_viable_products'] = count($planet['single_planet_products']) > 0;

            $characters[$planet['character_id']] = $planet['character_name'];
            $systemNames[$planet['system_id']] = $planet['system_name'];
            $planetRows[] = $planet;
        }

        $planetRows = array_values(array_filter(
            $planetRows,
            fn (array $planet): bool => $this->passesDashboardFilters($planet, $filters)
        ));

        usort($planetRows, function (array $a, array $b): int {
            return [
                $a['character_name'],
                $a['system_name'],
                $a['planet_name'],
            ] <=> [
                $b['character_name'],
                $b['system_name'],
                $b['planet_name'],
            ];
        });

        $statusCounts = [
            'active' => 0,
            'expired' => 0,
            'stalled' => 0,
        ];
        $extractorTotal = 0;
        $factoryTotal = 0;
        $minExpiry = null;

        foreach ($planetRows as $planet) {
            $statusCounts[$planet['status']]++;
            $extractorTotal += $planet['extractor_count'];
            $factoryTotal += $planet['factory_count'];
            if ($planet['next_expiry']) {
                $minExpiry = $minExpiry === null || $planet['next_expiry']->lt($minExpiry)
                    ? $planet['next_expiry']
                    : $minExpiry;
            }
        }

        return [
            'characters' => $characters,
            'systems' => $systemNames,
            'filters' => $filters,
            'summary' => [
                'character_count' => count(array_unique(array_column($planetRows, 'character_id'))),
                'colony_count' => count($planetRows),
                'extractor_count' => $extractorTotal,
                'factory_count' => $factoryTotal,
                'status_counts' => $statusCounts,
                'next_expiry' => $minExpiry,
                'next_expiry_human' => $minExpiry
                    ? $minExpiry->diffForHumans($now, ['parts' => 2, 'short' => true, 'syntax' => CarbonImmutable::DIFF_RELATIVE_TO_NOW])
                    : null,
            ],
            'colonies' => $planetRows,
        ];
    }

    public function recommendPlanetTypes(array $planetTypes): array
    {
        return app(PiRecommendationService::class)->analyzePlanetTypes($planetTypes);
    }

    public function getSystemCapabilityForProduct(string $systemQuery, string $productName): ?array
    {
        $system = app(SystemAnalyzerService::class)->getSystemDetails($systemQuery);
        if (! $system) {
            return null;
        }

        $recommendation = null;
        foreach ($system['recommendations'] as $item) {
            if (strcasecmp($item['name'], $productName) === 0) {
                $recommendation = $item;
                break;
            }
        }

        return [
            'system' => $system,
            'recommendation' => $recommendation,
            'can_build' => $recommendation !== null,
        ];
    }

    private function baseColonyRows()
    {
        $extractorAgg = DB::table('character_planet_extractors as cpe')
            ->leftJoin('character_planet_pins as cpp', function ($join): void {
                $join->on('cpp.character_id', '=', 'cpe.character_id')
                    ->on('cpp.planet_id', '=', 'cpe.planet_id')
                    ->on('cpp.pin_id', '=', 'cpe.pin_id');
            })
            ->leftJoin('invTypes as it', 'it.typeID', '=', 'cpe.product_type_id')
            ->selectRaw('cpe.character_id, cpe.planet_id, COUNT(*) as extractor_count')
            ->selectRaw('SUM(CASE WHEN cpe.cycle_time > 0 THEN (cpe.qty_per_cycle * 3600.0 / cpe.cycle_time) ELSE 0 END) as total_u_per_hour')
            ->selectRaw('MIN(cpp.expiry_time) as next_expiry')
            ->selectRaw("GROUP_CONCAT(DISTINCT it.typeName ORDER BY it.typeName SEPARATOR ', ') as extractor_products")
            ->groupBy('cpe.character_id', 'cpe.planet_id');

        $factoryAgg = DB::table('character_planet_factories as cpf')
            ->selectRaw('cpf.character_id, cpf.planet_id, COUNT(*) as factory_count')
            ->groupBy('cpf.character_id', 'cpf.planet_id');

        $contentAgg = DB::table('character_planet_contents as cpc')
            ->leftJoin('invTypes as cit', 'cit.typeID', '=', 'cpc.type_id')
            ->selectRaw('cpc.character_id, cpc.planet_id, COUNT(*) as storage_stacks, SUM(cpc.amount) as storage_total_amount')
            ->selectRaw("GROUP_CONCAT(DISTINCT cit.typeName ORDER BY cit.typeName SEPARATOR ', ') as content_names")
            ->groupBy('cpc.character_id', 'cpc.planet_id');

        return DB::table('character_planets as cp')
            ->leftJoin('character_infos as ci', 'ci.character_id', '=', 'cp.character_id')
            ->leftJoin('character_affiliations as ca', 'ca.character_id', '=', 'cp.character_id')
            ->leftJoin('corporation_infos as corp', 'corp.corporation_id', '=', 'ca.corporation_id')
            ->leftJoin('alliances as alliance', 'alliance.alliance_id', '=', 'ca.alliance_id')
            ->leftJoin('solar_systems as ss', 'ss.system_id', '=', 'cp.solar_system_id')
            ->leftJoin('regions as r', 'r.region_id', '=', 'ss.region_id')
            ->leftJoin('constellations as c', 'c.constellation_id', '=', 'ss.constellation_id')
            ->leftJoin('seat_pi_manager_static_planets as spp', 'spp.planet_id', '=', 'cp.planet_id')
            ->leftJoinSub($extractorAgg, 'extractors', function ($join): void {
                $join->on('extractors.character_id', '=', 'cp.character_id')
                    ->on('extractors.planet_id', '=', 'cp.planet_id');
            })
            ->leftJoinSub($factoryAgg, 'factories', function ($join): void {
                $join->on('factories.character_id', '=', 'cp.character_id')
                    ->on('factories.planet_id', '=', 'cp.planet_id');
            })
            ->leftJoinSub($contentAgg, 'contents', function ($join): void {
                $join->on('contents.character_id', '=', 'cp.character_id')
                    ->on('contents.planet_id', '=', 'cp.planet_id');
            })
            ->select([
                'cp.character_id',
                'cp.planet_id',
                'cp.solar_system_id',
                'cp.planet_type',
                'cp.upgrade_level',
                'cp.num_pins',
                'cp.last_update',
                'ci.name as character_name',
                'corp.name as corporation_name',
                'alliance.name as alliance_name',
                'ss.name as system_name',
                'r.name as region_name',
                'c.name as constellation_name',
                'spp.planet_name',
                'spp.planet_number',
                'extractors.extractor_count',
                'extractors.total_u_per_hour',
                'extractors.next_expiry',
                'extractors.extractor_products',
                'factories.factory_count',
                'contents.storage_stacks',
                'contents.storage_total_amount',
                'contents.content_names',
            ])
            ->get();
    }

    private function passesDashboardFilters(array $planet, array $filters): bool
    {
        $character = trim((string) ($filters['character'] ?? ''));
        if ($character !== '' && $planet['character_name'] !== $character) {
            return false;
        }

        $tiers = array_values(array_filter((array) ($filters['tiers'] ?? [])));
        if (count($tiers) > 0 && ! in_array($planet['highest_tier'], $tiers, true)) {
            return false;
        }

        $statuses = array_values(array_filter((array) ($filters['statuses'] ?? [])));
        if (count($statuses) > 0 && ! in_array($planet['status'], $statuses, true)) {
            return false;
        }

        return true;
    }

    private function splitCsv(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    private function highestTierForNames(array $names): ?string
    {
        $best = null;
        foreach ($names as $name) {
            $tier = $this->catalog->getTierForProduct($name);
            if (! $tier) {
                continue;
            }
            if ($best === null || (int) substr($tier, 1) > (int) substr($best, 1)) {
                $best = $tier;
            }
        }

        return $best;
    }

    private function deriveStatus(int $extractorCount, ?CarbonImmutable $nextExpiry, CarbonImmutable $now): string
    {
        if ($extractorCount === 0 || $nextExpiry === null) {
            return 'stalled';
        }
        if ($nextExpiry->lt($now)) {
            return 'expired';
        }

        return 'active';
    }

    private function normalizePlanetType(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match (strtolower($value)) {
            'temperate' => 'Temperate',
            'barren' => 'Barren',
            'oceanic' => 'Oceanic',
            'ice' => 'Ice',
            'gas' => 'Gas',
            'lava' => 'Lava',
            'storm' => 'Storm',
            'plasma' => 'Plasma',
            default => ucfirst($value),
        };
    }
}
