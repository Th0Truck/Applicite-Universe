<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    |
    | Permissions are grouped by area so the access seeder can create a clear
    | role matrix without scattering authorization strings through the codebase.
    |
    */

    'permissions' => [
        'users' => [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ],
        'roles' => [
            'roles.view',
            'roles.manage',
        ],
        'settings' => [
            'settings.view',
            'settings.manage',
        ],
        'pages' => [
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
        ],
        'security' => [
            'two_factor.enforce',
            'passkeys.manage',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Roles
    |--------------------------------------------------------------------------
    |
    | Role permissions are intentionally explicit. The super_admin role is still
    | granted every seeded permission, and AppServiceProvider lets it pass future
    | Gate checks without having to update this file for every new ability.
    |
    */

    'roles' => [
        'super_admin' => ['*'],
        'admin' => [
            'users.view',
            'users.create',
            'users.update',
            'roles.view',
            'pages.view',
            'pages.create',
            'pages.update',
            'pages.delete',
            'settings.view',
            'settings.manage',
            'two_factor.enforce',
            'passkeys.manage',
        ],
        'manager' => [
            'users.view',
            'users.update',
            'pages.view',
            'pages.update',
            'settings.view',
        ],
        'user' => [],
    ],
];
