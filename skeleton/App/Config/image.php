<?php

declare(strict_types=1);

/**
 * Image Setting & Configuration
 *
 * @author BenIyke <beniyk34@gmail.com> | (twitter:@BigBeniyke)
 */

return [
    'driver' => 'gd',
    'watermark' => [
        'path' => 'img/logo.png',
        'setting' => [
            'width' => 50,
            'height' => 50,
            'position' => 'bottom-right',
            'opacity' => 90,
            'padding' => [
                'x' => 10,
                'y' => 10,
            ],
        ],
    ],
    'presets' => [
        'small' => [
            'width' => 200,
            'height' => 200,
        ],
        'medium' => [
            'width' => 600,
            'height' => 400,
        ],
    ],
];
