<?php

declare(strict_types=1);

/**
 * This is the entry point for the Anchor PHP Framework
 *
 * @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */
ob_start();
require_once 'System/Core/init.php';
use Core\App;

$app = $container->get(App::class);
$app->run();
ob_end_flush();
