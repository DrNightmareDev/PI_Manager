<?php

return [
    'name' => 'SeAT PI Manager',
    'route_prefix' => 'pi-manager',
    'sidebar_permission' => 'seat-pi-manager.view',
    'languages' => ['en', 'de', 'zh-Hans'],
    'features' => [
        'system_analyzer' => true,
        'planner' => true,
        'compare' => false,
        'system_mix' => false,
        'market' => false,
        'dashboard' => false,
        'skyhooks' => false,
        'corporation' => false,
        'characters' => false,
        'translations' => false,
    ],
    'sde' => [
        'source' => 'local-seat-import',
        'import_static_planets' => true,
        'fuzzwork_denormalize_url' => 'https://www.fuzzwork.co.uk/dump/latest/mapDenormalize.sql.bz2',
    ],
];
