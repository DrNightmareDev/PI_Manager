<?php

return [
    'sidebar' => [
        'title' => 'PI Manager',
        'overview' => 'Uebersicht',
    ],
    'permissions' => [
        'view_label' => 'SeAT PI Manager anzeigen',
        'view_description' => 'Erlaubt den Zugriff auf die Seiten des SeAT PI Managers.',
        'configure_label' => 'SeAT PI Manager konfigurieren',
        'configure_description' => 'Erlaubt die Konfiguration von SeAT PI Manager Einstellungen und Importen.',
    ],
    'status' => [
        'bootstrap' => 'Bootstrap',
        'enabled' => 'Aktiv',
        'planned' => 'Geplant',
        'not_run' => 'Noch nicht ausgefuehrt',
    ],
    'fields' => [
        'plugin' => 'Plugin',
        'languages' => 'Sprachen',
        'next_focus' => 'Naechster Fokus',
        'static_planets' => 'Statische Planeten',
        'last_import' => 'Letzter Import',
        'last_status' => 'Letzter Status',
    ],
    'mvp' => [
        'shell' => 'SeAT-natives Plugin-Grundgeruest mit Menueeintrag und Berechtigungen',
        'install' => 'Installierbar aus lokalem Repo-Clone oder Release-Paket',
        'i18n' => 'Mehrsprachige Struktur von Anfang an',
        'system_analyzer' => 'System Analyzer als erstes funktionales Modul',
        'release' => 'GitHub Actions Build und Release-ZIP-Artefakte',
    ],
    'pages' => [
        'index' => [
            'title' => 'SeAT PI Manager',
            'header' => 'SeAT PI Manager Bootstrap',
            'subtitle' => 'Das Plugin-Grundgeruest ist bereit und vom bestehenden FastAPI-System getrennt.',
            'notice_title' => 'Wichtig:',
            'notice_body' => 'Dieses Paket wird bewusst parallel zum aktuellen Python-Stack aufgebaut. Der bestehende produktive Build bleibt unberuehrt.',
            'mvp_title' => 'MVP-Umfang',
            'features_title' => 'Aktuelle Bootstrap-Funktionen',
            'feature_flags_title' => 'Geplante Modul-Flags',
            'next_focus' => 'System Analyzer, statische Planeten und SeAT-native Datenfluesse',
            'imports_title' => 'Import statischer Daten',
            'command_title' => 'Import-Befehl:',
            'analyzer_title' => 'System-Analyzer-Track',
        ],
    ],
];
