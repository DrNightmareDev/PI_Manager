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
                'route' => 'seat-pi-manager.index',
            ],
        ],
    ],
];
