<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use DrNightmare\SeatPiManager\Services\StaticPlanetImportService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class OverviewController extends Controller
{
    public function index(StaticPlanetImportService $staticPlanetImportService): View
    {
        return view('seat-pi-manager::pages.overview', [
            'plugin_name' => config('seat-pi-manager.name', 'SeAT PI Manager'),
            'features' => config('seat-pi-manager.features', []),
            'languages' => config('seat-pi-manager.languages', ['en']),
            'static_planets' => $staticPlanetImportService->getSummary(),
        ]);
    }
}

