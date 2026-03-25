<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use DrNightmare\SeatPiManager\Services\SystemAnalyzerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SystemAnalyzerController extends Controller
{
    public function index(Request $request, SystemAnalyzerService $systemAnalyzerService): View
    {
        $systemQuery = (string) $request->query('system', '');
        $tierFilter = strtoupper(trim((string) $request->query('tier', '')));
        $singlePlanetOnly = (bool) $request->boolean('single_planet');
        $searchResults = $systemQuery !== '' ? $systemAnalyzerService->searchSystems($systemQuery) : [];
        $selectedSystem = $systemQuery !== '' ? $systemAnalyzerService->getSystemDetails($systemQuery) : null;

        if ($selectedSystem) {
            $selectedSystem['recommendations'] = array_values(array_filter(
                $selectedSystem['recommendations'],
                static function (array $item) use ($tierFilter, $singlePlanetOnly): bool {
                    if ($tierFilter !== '' && strtoupper((string) ($item['tier'] ?? '')) !== $tierFilter) {
                        return false;
                    }
                    if ($singlePlanetOnly && empty($item['single_planet_viable'])) {
                        return false;
                    }

                    return true;
                }
            ));
        }

        return view('seat-pi-manager::pages.system-analyzer', [
            'system_analyzer' => $systemAnalyzerService->getBootstrapSummary(),
            'system_query' => $systemQuery,
            'search_results' => $searchResults,
            'selected_system' => $selectedSystem,
            'tier_filter' => $tierFilter,
            'single_planet_only' => $singlePlanetOnly,
        ]);
    }
}
