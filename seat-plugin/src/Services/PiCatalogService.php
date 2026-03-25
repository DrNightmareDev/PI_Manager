<?php

declare(strict_types=1);

namespace DrNightmare\SeatPiManager\Services;

class PiCatalogService
{
    public function getP0ToP1(): array
    {
        return [
            'Aqueous Liquids' => 'Water',
            'Autotrophs' => 'Industrial Fibers',
            'Base Metals' => 'Reactive Metals',
            'Carbon Compounds' => 'Biofuels',
            'Complex Organisms' => 'Proteins',
            'Felsic Magma' => 'Silicon',
            'Heavy Metals' => 'Toxic Metals',
            'Ionic Solutions' => 'Electrolytes',
            'Micro Organisms' => 'Bacteria',
            'Noble Gas' => 'Oxygen',
            'Noble Metals' => 'Precious Metals',
            'Non-CS Crystals' => 'Chiral Structures',
            'Planktic Colonies' => 'Biomass',
            'Reactive Gas' => 'Oxidizing Compound',
            'Suspended Plasma' => 'Plasmoids',
        ];
    }

    public function getP1ToP2(): array
    {
        return [
            'Biocells' => ['Biofuels', 'Precious Metals'],
            'Construction Blocks' => ['Reactive Metals', 'Toxic Metals'],
            'Consumer Electronics' => ['Toxic Metals', 'Chiral Structures'],
            'Coolant' => ['Electrolytes', 'Water'],
            'Enriched Uranium' => ['Precious Metals', 'Toxic Metals'],
            'Fertilizer' => ['Bacteria', 'Proteins'],
            'Genetically Enhanced Livestock' => ['Proteins', 'Biomass'],
            'Livestock' => ['Proteins', 'Biofuels'],
            'Mechanical Parts' => ['Reactive Metals', 'Precious Metals'],
            'Microfiber Shielding' => ['Industrial Fibers', 'Silicon'],
            'Miniature Electronics' => ['Chiral Structures', 'Silicon'],
            'Nanites' => ['Bacteria', 'Reactive Metals'],
            'Oxides' => ['Oxidizing Compound', 'Oxygen'],
            'Polyaramids' => ['Oxidizing Compound', 'Industrial Fibers'],
            'Polytextiles' => ['Biofuels', 'Industrial Fibers'],
            'Rocket Fuel' => ['Plasmoids', 'Electrolytes'],
            'Silicate Glass' => ['Oxidizing Compound', 'Silicon'],
            'Superconductors' => ['Plasmoids', 'Water'],
            'Supertensile Plastics' => ['Oxygen', 'Biomass'],
            'Synthetic Oil' => ['Electrolytes', 'Oxygen'],
            'Test Cultures' => ['Bacteria', 'Water'],
            'Transmitter' => ['Plasmoids', 'Chiral Structures'],
            'Viral Agent' => ['Bacteria', 'Biomass'],
            'Water-Cooled CPU' => ['Reactive Metals', 'Water'],
        ];
    }

    public function getP2ToP3(): array
    {
        return [
            'Biotech Research Reports' => ['Nanites', 'Livestock', 'Construction Blocks'],
            'Camera Drones' => ['Silicate Glass', 'Rocket Fuel'],
            'Condensates' => ['Oxides', 'Coolant'],
            'Cryoprotectant Solution' => ['Test Cultures', 'Synthetic Oil', 'Fertilizer'],
            'Data Chips' => ['Supertensile Plastics', 'Microfiber Shielding'],
            'Gel-Matrix Biopaste' => ['Biocells', 'Oxides', 'Superconductors'],
            'Guidance Systems' => ['Water-Cooled CPU', 'Transmitter'],
            'Hazmat Detection Systems' => ['Polytextiles', 'Viral Agent', 'Transmitter'],
            'Hermetic Membranes' => ['Polyaramids', 'Genetically Enhanced Livestock'],
            'High-Tech Transmitters' => ['Polyaramids', 'Transmitter'],
            'Industrial Explosives' => ['Fertilizer', 'Polytextiles'],
            'Neocoms' => ['Biocells', 'Silicate Glass'],
            'Nuclear Reactors' => ['Microfiber Shielding', 'Enriched Uranium'],
            'Planetary Vehicles' => ['Supertensile Plastics', 'Mechanical Parts', 'Miniature Electronics'],
            'Robotics' => ['Mechanical Parts', 'Consumer Electronics'],
            'Smartfab Units' => ['Construction Blocks', 'Miniature Electronics'],
            'Supercomputers' => ['Water-Cooled CPU', 'Coolant', 'Consumer Electronics'],
            'Synthetic Synapses' => ['Supertensile Plastics', 'Test Cultures'],
            'Transcranial Microcontrollers' => ['Biocells', 'Nanites'],
            'Ukomi Super Conductors' => ['Synthetic Oil', 'Superconductors'],
            'Vaccines' => ['Livestock', 'Viral Agent'],
        ];
    }

    public function getP3ToP4(): array
    {
        return [
            'Broadcast Node' => ['Neocoms', 'Data Chips', 'High-Tech Transmitters'],
            'Integrity Response Drones' => ['Gel-Matrix Biopaste', 'Hazmat Detection Systems', 'Planetary Vehicles'],
            'Nano-Factory' => ['Industrial Explosives', 'Ukomi Super Conductors', 'Reactive Metals'],
            'Organic Mortar Applicators' => ['Condensates', 'Robotics', 'Bacteria'],
            'Recursive Computing Module' => ['Synthetic Synapses', 'Guidance Systems', 'Transcranial Microcontrollers'],
            'Self-Harmonizing Power Core' => ['Camera Drones', 'Nuclear Reactors', 'Hermetic Membranes'],
            'Sterile Conduits' => ['Smartfab Units', 'Vaccines', 'Water'],
            'Wetware Mainframe' => ['Supercomputers', 'Biotech Research Reports', 'Cryoprotectant Solution'],
        ];
    }

    public function getPlanetResources(): array
    {
        return [
            'Barren' => ['Aqueous Liquids', 'Base Metals', 'Carbon Compounds', 'Micro Organisms', 'Noble Metals'],
            'Gas' => ['Aqueous Liquids', 'Base Metals', 'Ionic Solutions', 'Noble Gas', 'Reactive Gas'],
            'Ice' => ['Aqueous Liquids', 'Heavy Metals', 'Micro Organisms', 'Noble Gas', 'Planktic Colonies'],
            'Lava' => ['Base Metals', 'Felsic Magma', 'Heavy Metals', 'Non-CS Crystals', 'Suspended Plasma'],
            'Oceanic' => ['Aqueous Liquids', 'Carbon Compounds', 'Complex Organisms', 'Micro Organisms', 'Planktic Colonies'],
            'Plasma' => ['Base Metals', 'Heavy Metals', 'Noble Metals', 'Non-CS Crystals', 'Suspended Plasma'],
            'Storm' => ['Aqueous Liquids', 'Base Metals', 'Ionic Solutions', 'Noble Gas', 'Suspended Plasma'],
            'Temperate' => ['Aqueous Liquids', 'Autotrophs', 'Carbon Compounds', 'Complex Organisms', 'Micro Organisms'],
        ];
    }

    public function getPlanetTypeColors(): array
    {
        return [
            'Storm' => '#5b8de4',
            'Barren' => '#a67c52',
            'Gas' => '#7fb069',
            'Lava' => '#e63946',
            'Oceanic' => '#2980b9',
            'Plasma' => '#9b59b6',
            'Temperate' => '#27ae60',
            'Ice' => '#74b9ff',
        ];
    }

    public function getAllProducts(): array
    {
        return [
            'P1' => array_values(array_unique(array_values($this->getP0ToP1()))),
            'P2' => array_keys($this->getP1ToP2()),
            'P3' => array_keys($this->getP2ToP3()),
            'P4' => array_keys($this->getP3ToP4()),
        ];
    }

    public function getTierForProduct(string $product): ?string
    {
        foreach ($this->getAllProducts() as $tier => $products) {
            if (in_array($product, $products, true)) {
                return $tier;
            }
        }

        return null;
    }

    public function getInputsForProduct(string $product): array
    {
        $p0ToP1 = $this->getP0ToP1();
        $p1ToP2 = $this->getP1ToP2();
        $p2ToP3 = $this->getP2ToP3();
        $p3ToP4 = $this->getP3ToP4();
        $p1Reverse = array_flip($p0ToP1);

        if (isset($p1Reverse[$product])) {
            return [$p1Reverse[$product]];
        }
        if (isset($p1ToP2[$product])) {
            return $p1ToP2[$product];
        }
        if (isset($p2ToP3[$product])) {
            return $p2ToP3[$product];
        }
        if (isset($p3ToP4[$product])) {
            return $p3ToP4[$product];
        }

        return [];
    }

    public function getRequiredP0(string $product): array
    {
        $tier = $this->getTierForProduct($product);
        if ($tier === null) {
            return [];
        }

        return array_values(array_unique($this->collectP0($product, $tier)));
    }

    public function getRequiredPlanetTypes(string $product): array
    {
        $p0 = $this->getRequiredP0($product);
        $results = [];

        foreach ($this->getPlanetResources() as $planetType => $resources) {
            if (count(array_intersect($p0, $resources)) > 0) {
                $results[] = $planetType;
            }
        }

        return $results;
    }

    public function getPlannerSummary(string $product): ?array
    {
        $tier = $this->getTierForProduct($product);
        if ($tier === null) {
            return null;
        }

        return [
            'name' => $product,
            'tier' => $tier,
            'inputs' => $this->getInputsForProduct($product),
            'required_p0' => $this->getRequiredP0($product),
            'required_planet_types' => $this->getRequiredPlanetTypes($product),
        ];
    }

    private function collectP0(string $product, string $tier): array
    {
        if ($tier === 'P1') {
            $reverse = array_flip($this->getP0ToP1());
            return isset($reverse[$product]) ? [$reverse[$product]] : [];
        }

        $results = [];
        foreach ($this->getInputsForProduct($product) as $input) {
            $inputTier = $this->getTierForProduct($input);
            if ($inputTier === null) {
                continue;
            }
            $results = array_merge($results, $this->collectP0($input, $inputTier));
        }

        return $results;
    }
}

