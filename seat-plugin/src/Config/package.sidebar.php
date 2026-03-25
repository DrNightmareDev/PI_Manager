<?php

return [
    'seat-pi-manager' => [
        'permission'    => 'seat-pi-manager.view',
        'name'          => 'PI Manager',
        'icon'          => 'fas fa-globe',
        'route_segment' => 'pi-manager',
        'entries'       => [
            [
                'name'  => 'Dashboard',
                'icon'  => 'fas fa-table',
                'route' => 'seat-pi-manager.dashboard',
            ],
            [
                'name'  => 'Overview',
                'icon'  => 'fas fa-satellite-dish',
                'route' => 'seat-pi-manager.overview',
            ],
            [
                'name'  => 'System Analyzer',
                'icon'  => 'fas fa-globe-europe',
                'route' => 'seat-pi-manager.system-analyzer',
            ],
            [
                'name'  => 'PI Chain Planner',
                'icon'  => 'fas fa-project-diagram',
                'route' => 'seat-pi-manager.planner',
            ],
            [
                'name'  => 'System Mix',
                'icon'  => 'fas fa-layer-group',
                'route' => 'seat-pi-manager.system-mix',
            ],
        ],
    ],
];
