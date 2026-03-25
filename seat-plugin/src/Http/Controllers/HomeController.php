<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('seat-pi-manager::pages.index', [
            'plugin_name' => config('seat-pi-manager.name', 'SeAT PI Manager'),
            'features' => config('seat-pi-manager.features', []),
            'languages' => config('seat-pi-manager.languages', ['en']),
        ]);
    }
}
