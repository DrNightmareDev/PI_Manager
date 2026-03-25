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
        $searchResults = $systemQuery !== '' ? $systemAnalyzerService->searchSystems($systemQuery) : [];
        $selectedSystem = $systemQuery !== '' ? $systemAnalyzerService->getSystemDetails($systemQuery) : null;

        return view('seat-pi-manager::pages.system-analyzer', [
            'system_analyzer' => $systemAnalyzerService->getBootstrapSummary(),
            'system_query' => $systemQuery,
            'search_results' => $searchResults,
            'selected_system' => $selectedSystem,
        ]);
    }
}

