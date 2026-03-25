<?php

return [
    'seat-pi-manager' => [
        'permission'    => 'seat-pi-manager.view',
        'name'          => 'seat-pi-manager::messages.sidebar.title',
        'icon'          => 'fas fa-globe',
        'route_segment' => 'pi-manager',
        'entries'       => [
            [
                'name'  => 'seat-pi-manager::messages.sidebar.overview',
                'icon'  => 'fas fa-satellite-dish',
                'route' => 'seat-pi-manager.index',
            ],
        ],
    ],
];
