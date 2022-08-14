<?php

declare(strict_types=1);

return [
    'name' => 'Demo',
    'require' => [
        'hcms_version' => '0.10.0',
        'composer' => [
            'hyperf/async-queue' => '^2.2'
        ],
        'module' => ['admin']
    ],
    'version' => '0.2.0'
];