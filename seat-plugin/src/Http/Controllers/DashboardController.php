<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use DrNightmare\SeatPiManager\Services\SeatPiDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index(Request $request, SeatPiDataService $seatPiDataService): View
    {
        $filters = [
            'character' => trim((string) $request->query('character', '')),
            'tiers' => array_values(array_filter(array_map('strtoupper', (array) $request->query('tier', [])))),
            'statuses' => array_values(array_filter(array_map('strtolower', (array) $request->query('status', [])))),
        ];

        $dashboard = $seatPiDataService->getDashboardData($filters);

        return view('seat-pi-manager::pages.dashboard', [
            'dashboard' => $dashboard,
            'planet_type_colors' => app(\DrNightmare\SeatPiManager\Services\PiCatalogService::class)->getPlanetTypeColors(),
        ]);
    }
}
