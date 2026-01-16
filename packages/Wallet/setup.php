<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Package Setup
 *
 * This file is used by the package installer to configure the Wallet package.
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */
return [
    'providers' => [
        Wallet\Providers\WalletServiceProvider::class,
    ]
];
