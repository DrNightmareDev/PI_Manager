<?php

return [
    'name' => 'SeAT PI Manager',
    'route_prefix' => 'pi-manager',
    'sidebar_permission' => 'seat-pi-manager.view',
    'languages' => ['en', 'de', 'zh-Hans'],
    'features' => [
        'system_analyzer' => true,
        'planner' => true,
        'compare' => true,
        'system_mix' => true,
        'market' => true,
        'dashboard' => true,
        'skyhooks' => true,
        'corporation' => true,
        'characters' => true,
        'translations' => true,
    ],
    'sde' => [
        'source' => 'local-seat-import',
        'import_static_planets' => true,
        'fuzzwork_denormalize_url' => 'https://www.fuzzwork.co.uk/dump/latest/mapDenormalize.sql.bz2',
    ],
];
