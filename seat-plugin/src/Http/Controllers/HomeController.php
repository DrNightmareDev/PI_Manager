<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use DrNightmare\SeatPiManager\Services\StaticPlanetImportService;
use DrNightmare\SeatPiManager\Services\SystemAnalyzerService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(
        Request $request,
        StaticPlanetImportService $staticPlanetImportService,
        SystemAnalyzerService $systemAnalyzerService,
    ): View
    {
        $systemQuery = (string) $request->query('system', '');
        $searchResults = $systemQuery !== '' ? $systemAnalyzerService->searchSystems($systemQuery) : [];
        $selectedSystem = $systemQuery !== '' ? $systemAnalyzerService->getSystemDetails($systemQuery) : null;

        return view('seat-pi-manager::pages.index', [
            'plugin_name' => config('seat-pi-manager.name', 'SeAT PI Manager'),
            'features' => config('seat-pi-manager.features', []),
            'languages' => config('seat-pi-manager.languages', ['en']),
            'static_planets' => $staticPlanetImportService->getSummary(),
            'system_analyzer' => $systemAnalyzerService->getBootstrapSummary(),
            'system_query' => $systemQuery,
            'search_results' => $searchResults,
            'selected_system' => $selectedSystem,
        ]);
    }
}
