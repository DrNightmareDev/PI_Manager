<?php

return [
    'sidebar' => [
        'title' => 'PI 管理器',
        'overview' => '总览',
    ],
    'permissions' => [
        'view_label' => '查看 SeAT PI 管理器',
        'view_description' => '允许访问 SeAT PI 管理器页面。',
        'configure_label' => '配置 SeAT PI 管理器',
        'configure_description' => '允许配置 SeAT PI 管理器设置和导入。',
    ],
    'status' => [
        'bootstrap' => '引导阶段',
        'enabled' => '已启用',
        'planned' => '计划中',
        'not_run' => '尚未运行',
    ],
    'fields' => [
        'plugin' => '插件',
        'languages' => '语言',
        'next_focus' => '下一步重点',
        'static_planets' => '静态行星',
        'last_import' => '最近导入',
        'last_status' => '最近状态',
    ],
    'mvp' => [
        'shell' => '原生 SeAT 插件骨架，含菜单与权限',
        'install' => '支持从本地仓库克隆或发布包安装',
        'i18n' => '从一开始就支持多语言结构',
        'system_analyzer' => '星系分析器作为首个功能模块',
        'release' => 'GitHub Actions 构建与发布 ZIP 制品',
    ],
    'pages' => [
        'index' => [
            'title' => 'SeAT PI 管理器',
            'header' => 'SeAT PI 管理器引导页',
            'subtitle' => '插件骨架已经就绪，并与现有 FastAPI 应用完全隔离。',
            'notice_title' => '重要提示：',
            'notice_body' => '该插件会与当前 Python 生产栈并行构建，不会改动现有生产环境。',
            'mvp_title' => 'MVP 范围',
            'features_title' => '当前引导能力',
            'feature_flags_title' => '计划中的模块开关',
            'next_focus' => '星系分析器、静态行星数据与 SeAT 原生数据流',
            'imports_title' => '静态数据导入',
            'command_title' => '导入命令：',
            'analyzer_title' => '星系分析器轨道',
        ],
    ],
];
