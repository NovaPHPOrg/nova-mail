<?php

declare(strict_types=1);

return [
    'config' => [
        'framework_start' => [
            'nova\\plugin\\mail\\MailPluginManager',
        ],
    ],
    'require' => [
        'login',
        'tpl',
    ],
];
