<?php

declare(strict_types=1);

/**
 * This PHP script initializes the application, processes pending jobs in the
 * queue, and exits with a response.
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */

require_once __DIR__.'/System/Core/init.php';

if (! function_exists('job')) {
    throw new RuntimeException('The job function is not available. Please ensure the function is defined.');
}

try {
    $dispatcher = job();

    defer(function () use ($dispatcher) {
        $response = $dispatcher->failed()->run();
        echo $response.PHP_EOL;
    });

    $response = $dispatcher->pending()->run();
    $deferrer = Core\Deferrer::getInstance();

    if ($deferrer->hasPayload()) {
        foreach ($deferrer->getPayloads() as $payload) {
            if (is_callable($payload)) {
                call_user_func($payload);
            } else {
                error_log('Deferred payload is not callable: '.print_r($payload, true));
            }
        }
    }

} catch (Exception $e) {
    $response = 'Error: '.$e->getMessage();
}

exit($response);
