<?php

declare(strict_types=1);

/**
 * This PHP script initializes the application, processes pending jobs in the
 * queue, and exits with a response.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

require_once __DIR__ . '/System/Core/init.php';

use Core\Kernel;
use Core\Services\BackgroundDispatcherInterface;
use Exception;
use Throwable;

try {
    $runner = resolve(BackgroundDispatcherInterface::class);
    $response = $runner->run();
    echo $response . PHP_EOL;

    if (function_exists('resolve')) {
        try {
            $kernel = resolve(Kernel::class);
            $kernel->terminate();
        } catch (Throwable $e) {
            // Ignore termination errors in cron
        }
    }
} catch (Exception $e) {
    $response = 'Critical Background Dispatch Error: ' . $e->getMessage();
    echo $response . PHP_EOL;
}

exit($response);
