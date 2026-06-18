<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Page Templates
    |--------------------------------------------------------------------------
    |
    | Templates define how CMS pages are rendered on the public site. The keys
    | are stored on pages so views can evolve without changing existing content.
    |
    */

    'templates' => [
        'standard' => [
            'label' => 'Standard content',
            'view' => 'cms.templates.standard',
        ],
        'feature' => [
            'label' => 'Feature sections',
            'view' => 'cms.templates.feature',
        ],
    ],
];
