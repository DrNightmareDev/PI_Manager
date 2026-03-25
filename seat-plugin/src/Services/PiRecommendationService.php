<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Services;

class PiRecommendationService
{
    public function __construct(
        private readonly PiCatalogService $catalog,
    ) {
    }

    public function analyzePlanetTypes(array $planetTypes): array
    {
        $planetTypes = array_values(array_filter(array_unique($planetTypes)));
        $planetResources = $this->catalog->getPlanetResources();
        $p0ToP1 = $this->catalog->getP0ToP1();
        $p1ToP2 = $this->catalog->getP1ToP2();
        $p2ToP3 = $this->catalog->getP2ToP3();
        $p3ToP4 = $this->catalog->getP3ToP4();

        $results = [];

        $availableP0 = [];
        foreach ($planetTypes as $planetType) {
            foreach (($planetResources[$planetType] ?? []) as $resource) {
                $availableP0[$resource] = true;
            }
        }

        $availableP1 = [];
        foreach ($p0ToP1 as $p0 => $p1) {
            if (isset($availableP0[$p0])) {
                $availableP1[$p1] = true;
            }
        }

        foreach (array_keys($availableP1) as $p1) {
            $singlePlanetTypes = $this->singlePlanetTypesForP1Inputs([$p1], $planetTypes);
            $p0Inputs = [];
            foreach ($p0ToP1 as $p0 => $out) {
                if ($out === $p1) {
                    $p0Inputs[] = $p0;
                }
            }
            $results[] = [
                'name' => $p1,
                'tier' => 'P1',
                'inputs' => array_slice($p0Inputs, 0, 1),
                'planets_needed' => $this->planetsForP0($p0Inputs[0] ?? '', $planetTypes),
                'single_planet_types' => $singlePlanetTypes,
                'single_planet_viable' => count($singlePlanetTypes) > 0,
                'all_p1_inputs' => [$p1],
                'available' => true,
                'score' => 10,
            ];
        }

        $availableP2 = [];
        foreach ($p1ToP2 as $p2 => $inputs) {
            if ($this->allPresent($inputs, $availableP1)) {
                $availableP2[$p2] = true;
            }
        }

        foreach (array_keys($availableP2) as $p2) {
            $inputs = $p1ToP2[$p2] ?? [];
            $singlePlanetTypes = $this->singlePlanetTypesForP1Inputs($inputs, $planetTypes);
            $results[] = [
                'name' => $p2,
                'tier' => 'P2',
                'inputs' => $inputs,
                'planets_needed' => $this->planetsForP1List($inputs, $planetTypes),
                'single_planet_types' => $singlePlanetTypes,
                'single_planet_viable' => count($singlePlanetTypes) > 0,
                'all_p1_inputs' => $this->allP1ForProduct($p2, 'P2'),
                'available' => true,
                'score' => 25,
            ];
        }

        $availableP3 = [];
        foreach ($p2ToP3 as $p3 => $inputs) {
            if ($this->allPresent($inputs, $availableP2)) {
                $availableP3[$p3] = true;
            }
        }

        foreach (array_keys($availableP3) as $p3) {
            $inputs = $p2ToP3[$p3] ?? [];
            $neededPlanets = [];
            foreach ($inputs as $p2) {
                foreach ($this->planetsForP1List($p1ToP2[$p2] ?? [], $planetTypes) as $planetType) {
                    $neededPlanets[$planetType] = true;
                }
            }
            $singlePlanetTypes = $this->singlePlanetTypesForP1Inputs($this->allP1ForProduct($p3, 'P3'), $planetTypes);
            $results[] = [
                'name' => $p3,
                'tier' => 'P3',
                'inputs' => $inputs,
                'planets_needed' => array_values(array_keys($neededPlanets)),
                'single_planet_types' => $singlePlanetTypes,
                'single_planet_viable' => count($singlePlanetTypes) > 0,
                'all_p1_inputs' => $this->allP1ForProduct($p3, 'P3'),
                'available' => true,
                'score' => 60,
            ];
        }

        $hasAdvancedPlanet = count(array_intersect($planetTypes, ['Barren', 'Temperate'])) > 0;
        if ($hasAdvancedPlanet) {
            foreach ($p3ToP4 as $p4 => $inputs) {
                if (! $this->allPresentHybrid($inputs, $availableP3, $availableP1)) {
                    continue;
                }
                $neededPlanets = [];
                foreach ($inputs as $input) {
                    if (! isset($p2ToP3[$input])) {
                        continue;
                    }
                    foreach ($p2ToP3[$input] as $p2) {
                        foreach ($this->planetsForP1List($p1ToP2[$p2] ?? [], $planetTypes) as $planetType) {
                            $neededPlanets[$planetType] = true;
                        }
                    }
                }
                foreach (['Barren', 'Temperate'] as $advanced) {
                    if (in_array($advanced, $planetTypes, true)) {
                        $neededPlanets[$advanced] = true;
                    }
                }
                $singlePlanetTypes = $this->singlePlanetTypesForP1Inputs($this->allP1ForProduct($p4, 'P4'), $planetTypes);
                $results[] = [
                    'name' => $p4,
                    'tier' => 'P4',
                    'inputs' => $inputs,
                    'planets_needed' => array_values(array_keys($neededPlanets)),
                    'single_planet_types' => $singlePlanetTypes,
                    'single_planet_viable' => count($singlePlanetTypes) > 0,
                    'all_p1_inputs' => $this->allP1ForProduct($p4, 'P4'),
                    'available' => true,
                    'score' => 150,
                ];
            }
        }

        $tierOrder = ['P4' => 4, 'P3' => 3, 'P2' => 2, 'P1' => 1];
        usort($results, fn (array $a, array $b) => [$tierOrder[$b['tier']] ?? 0, $b['score']] <=> [$tierOrder[$a['tier']] ?? 0, $a['score']]);

        return $results;
    }

    private function allP1ForProduct(string $name, string $tier): array
    {
        $p1ToP2 = $this->catalog->getP1ToP2();
        $p2ToP3 = $this->catalog->getP2ToP3();
        $p3ToP4 = $this->catalog->getP3ToP4();

        if ($tier === 'P1') {
            return [$name];
        }
        if ($tier === 'P2') {
            return array_values($p1ToP2[$name] ?? []);
        }
        if ($tier === 'P3') {
            $inputs = [];
            foreach (($p2ToP3[$name] ?? []) as $p2) {
                foreach (($p1ToP2[$p2] ?? []) as $p1) {
                    $inputs[$p1] = true;
                }
            }
            return array_values(array_keys($inputs));
        }
        if ($tier === 'P4') {
            $inputs = [];
            foreach (($p3ToP4[$name] ?? []) as $input) {
                if (isset($p2ToP3[$input])) {
                    foreach ($p2ToP3[$input] as $p2) {
                        foreach (($p1ToP2[$p2] ?? []) as $p1) {
                            $inputs[$p1] = true;
                        }
                    }
                } elseif (isset($p1ToP2[$input])) {
                    foreach ($p1ToP2[$input] as $p1) {
                        $inputs[$p1] = true;
                    }
                } else {
                    $inputs[$input] = true;
                }
            }
            return array_values(array_keys($inputs));
        }

        return [];
    }

    private function planetsForP0(string $p0, array $availablePlanets): array
    {
        $results = [];
        foreach ($this->catalog->getPlanetResources() as $planetType => $resources) {
            if (in_array($planetType, $availablePlanets, true) && in_array($p0, $resources, true)) {
                $results[] = $planetType;
            }
        }
        return $results;
    }

    private function planetsForP1List(array $p1Inputs, array $availablePlanets): array
    {
        $results = [];
        foreach ($p1Inputs as $p1) {
            foreach ($this->catalog->getP0ToP1() as $p0 => $out) {
                if ($out !== $p1) {
                    continue;
                }
                foreach ($this->planetsForP0($p0, $availablePlanets) as $planetType) {
                    $results[$planetType] = true;
                    break;
                }
            }
        }
        return array_values(array_keys($results));
    }

    private function singlePlanetTypesForP1Inputs(array $p1Inputs, array $availablePlanets): array
    {
        $requiredP0 = [];
        foreach ($p1Inputs as $p1) {
            foreach ($this->catalog->getP0ToP1() as $p0 => $out) {
                if ($out === $p1) {
                    $requiredP0[$p0] = true;
                }
            }
        }
        if (count($requiredP0) === 0) {
            return [];
        }

        $matches = [];
        foreach ($availablePlanets as $planetType) {
            $resources = $this->catalog->getPlanetResources()[$planetType] ?? [];
            if (count(array_diff(array_keys($requiredP0), $resources)) === 0) {
                $matches[] = $planetType;
            }
        }

        sort($matches);
        return array_values(array_unique($matches));
    }

    private function allPresent(array $inputs, array $available): bool
    {
        foreach ($inputs as $input) {
            if (! isset($available[$input])) {
                return false;
            }
        }
        return true;
    }

    private function allPresentHybrid(array $inputs, array $availableP3, array $availableP1): bool
    {
        foreach ($inputs as $input) {
            if (! isset($availableP3[$input]) && ! isset($availableP1[$input])) {
                return false;
            }
        }
        return true;
    }
}

