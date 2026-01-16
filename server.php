<?php

declare(strict_types=1);

/**
 * Handles HTTP requests for the PHP built-in server.
 * Routes non-public requests to index.php and protects sensitive paths.
 *
 *  @author BenIyke <beniyke34@gmail.com> | (twitter:@BigBeniyke)
 */
function handleError(int $status_code): void
{
    http_response_code($status_code);
    include __DIR__.'/System/Core/Views/Templates/notfound.html';
    exit;
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Prevent access to hidden files or directories (e.g., /.env)
if (strpos($uri, '/.') === 0) {
    handleError(403);
}

// Only allow serving of these file types directly
$public_file_types = [
    'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'zip', 'tar', 'gz', 'rar', 'woff', 'woff2', 'xml', 'txt',
];

$path = __DIR__.$uri;
$ext = pathinfo($uri, PATHINFO_EXTENSION);

// Normalize path to prevent directory traversal
$real_path = realpath($path);
$is_public_file = $real_path !== false && strpos($real_path, __DIR__) === 0 && file_exists($real_path) && in_array($ext, $public_file_types);

if ($is_public_file) {
    return false; // Let PHP server handle the file
}

require __DIR__.'/index.php';
