<?php

declare(strict_types=1);

return [
    'db_dependent' => [
        'tenant:create',
        'tenant:list',
        'tenant:migrate',
        'worker:start',
        'queue:run',
        'worker:restart',
        'queue:check',
        'queue:flush',
        'queue:status'
    ],
];
