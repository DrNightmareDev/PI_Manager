<?php

return [
    'sidebar' => [
        'title' => 'PI Manager',
        'overview' => 'Overview',
    ],
    'permissions' => [
        'view_label' => 'View SeAT PI Manager',
        'view_description' => 'Allows access to the SeAT PI Manager pages.',
        'configure_label' => 'Configure SeAT PI Manager',
        'configure_description' => 'Allows configuration of SeAT PI Manager settings and imports.',
    ],
    'status' => [
        'bootstrap' => 'Bootstrap',
        'enabled' => 'Enabled',
        'planned' => 'Planned',
    ],
    'fields' => [
        'plugin' => 'Plugin',
        'languages' => 'Languages',
        'next_focus' => 'Next focus',
    ],
    'mvp' => [
        'shell' => 'SeAT-native plugin shell with menu entry and permissions',
        'install' => 'Installable from a local repository clone or release package',
        'i18n' => 'Multilingual structure from the beginning',
        'system_analyzer' => 'System Analyzer as the first functional module',
        'release' => 'GitHub Actions build and release ZIP artifacts',
    ],
    'pages' => [
        'index' => [
            'title' => 'SeAT PI Manager',
            'header' => 'SeAT PI Manager Bootstrap',
            'subtitle' => 'The plugin skeleton is ready and isolated from the existing FastAPI application.',
            'notice_title' => 'Important:',
            'notice_body' => 'This package is intentionally scaffolded in parallel to the current Python stack. The existing production build remains untouched.',
            'mvp_title' => 'MVP scope',
            'features_title' => 'Current bootstrap features',
            'feature_flags_title' => 'Planned module flags',
            'next_focus' => 'System Analyzer, static planets, and SeAT-native data flows',
        ],
    ],
];
