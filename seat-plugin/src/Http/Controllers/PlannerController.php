<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Http\Controllers;

use DrNightmare\SeatPiManager\Services\PiCatalogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PlannerController extends Controller
{
    public function index(Request $request, PiCatalogService $piCatalogService): View
    {
        $productQuery = trim((string) $request->query('product', ''));
        $catalog = $piCatalogService->getAllProducts();
        $selected = $productQuery !== '' ? $piCatalogService->getPlannerSummary($productQuery) : null;

        $matchingProducts = [];
        if ($productQuery !== '') {
            foreach ($catalog as $tier => $products) {
                foreach ($products as $product) {
                    if (mb_stripos($product, $productQuery) !== false) {
                        $matchingProducts[] = [
                            'name' => $product,
                            'tier' => $tier,
                        ];
                    }
                }
            }
        }

        return view('seat-pi-manager::pages.planner', [
            'catalog' => $catalog,
            'selected_product' => $selected,
            'product_query' => $productQuery,
            'matching_products' => $matchingProducts,
            'planet_type_colors' => $piCatalogService->getPlanetTypeColors(),
            'planet_resources' => $piCatalogService->getPlanetResources(),
        ]);
    }
}

