<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use DrNightmare\SeatPiManager\Services\SystemAnalyzerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SystemMixController extends Controller
{
    public function index(Request $request, SystemAnalyzerService $systemAnalyzerService): View
    {
        $systemQuery = trim((string) $request->query('systems', ''));
        $normalized = str_replace(["\r\n", "\n", ';'], ',', $systemQuery);
        $systemNames = array_values(array_unique(array_filter(array_map('trim', explode(',', $normalized)))));

        $systems = [];
        $planetTypeCounts = [];
        $recommendations = [];
        $planetCount = 0;

        if (count($systemNames) > 0) {
            $mix = $systemAnalyzerService->analyzeSystemMix($systemNames);
            $systems = $mix['systems'];
            $planetTypeCounts = $mix['planet_type_summary'];
            $recommendations = $mix['recommendations'];
            $planetCount = $mix['planet_count'];
        }

        return view('seat-pi-manager::pages.system-mix', [
            'systems_query' => $systemQuery,
            'selected_systems' => $systems,
            'planet_type_summary' => $planetTypeCounts,
            'recommendations' => $recommendations,
            'planet_count' => $planetCount,
        ]);
    }
}
