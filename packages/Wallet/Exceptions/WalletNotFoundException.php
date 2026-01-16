<?php

declare(strict_types=1);

/**
 * Anchor Framework
 *
 * Wallet Not Found Exception
 *
 * @author BenIyke <beniyke34@gmail.com> | Twitter: @BigBeniyke
 */

namespace Wallet\Exceptions;

use Exception;

class WalletNotFoundException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("Wallet not found: {$identifier}", 404);
    }
}
