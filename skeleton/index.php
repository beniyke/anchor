<?php

declare(strict_types=1);

/**
 * Entry point for the Anchor Skeleton
 */
ob_start();

// Use vendor for managed mode, or fallback to System for standalone
$bootstrapper = file_exists(__DIR__ . '/vendor/autoload.php')
    ? __DIR__ . '/vendor/beniyke/framework/System/Core/init.php'
    : __DIR__ . '/System/Core/init.php';

if (!file_exists($bootstrapper)) {
    die("Framework not found. Run 'php dock' or 'composer install'.");
}

require_once $bootstrapper;

use Core\App;

$app = $container->get(App::class);
$app->run();
ob_end_flush();
