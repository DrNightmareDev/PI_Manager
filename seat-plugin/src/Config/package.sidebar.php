<?php

return [
    'seat-pi-manager' => [
        'permission'    => 'seat-pi-manager.view',
        'name'          => 'PI Manager',
        'icon'          => 'fas fa-globe',
        'route_segment' => 'pi-manager',
        'entries'       => [
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
        ],
    ],
];
