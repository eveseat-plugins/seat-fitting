<?php

return [
    'doctrine' => [
        'name' => 'Doctrines & Fittings',
        'label' => 'fitting::config.menu_title',
        'permission' => 'fitting.doctrineview',
        'route_segment' => 'fitting',
        'icon' => 'fas fa-rocket',
        'entries' => [
            'fitting' => [
                'label' => 'fitting::config.menu_fitting',
                'name' => 'Fittings',
                'icon' => 'fas fa-rocket',
                'route_segment' => 'fitting',
                'route' => 'cryptafitting::view',
                'permission' => 'fitting.view',
            ],
            'doctrine' => [
                'label' => 'fitting::config.menu_doctrines',
                'name' => 'Doctrine',
                'icon' => 'fas fa-list',
                'route_segment' => 'fitting',
                'route' => 'cryptafitting::doctrineview',
                'permission' => 'fitting.doctrineview',
            ],
            'doctrinereport' => [
                'label' => 'fitting::config.menu_doctrine_report',
                'name' => 'Doctrine Report',
                'icon' => 'fas fa-chart-pie',
                'route_segment' => 'fitting',
                'route' => 'cryptafitting::doctrinereport',
                'permission' => 'fitting.reportview',
            ],
            'about' => [
                'label' => 'fitting::config.menu_about',
                'name' => 'About',
                'icon' => 'fas fa-info',
                'route_segment' => 'fitting',
                'route' => 'cryptafitting::about',
                'permission' => 'fitting.view',
            ],
            'settings' => [
                'label' => 'fitting::config.menu_settings',
                'name' => 'Settings',
                'icon' => 'fas fa-cog',
                'route_segment' => 'fitting',
                'route' => 'fitting.settings',
                'permission' => 'fitting.settings',
            ],
        ],
    ],
];
