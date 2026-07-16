<?php

// Development-only server router for php -S.
// In production, use nginx + php-fpm as configured in .deploy/nginx.conf

if (($_ENV['APP_ENV'] ?? 'local') === 'production') {
    http_response_code(403);
    echo 'server.php cannot be used in production mode. Use nginx + php-fpm.';
    exit;
}

$publicPath = __DIR__ . '/public';

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    if (str_starts_with($uri, '/storage/')) {
        $realPath = realpath($publicPath.$uri);
        if ($realPath && file_exists($realPath)) {
            $mime = mime_content_type($realPath);
            if ($mime) {
                header('Content-Type: ' . $mime);
            }
            header('Content-Length: ' . filesize($realPath));
            readfile($realPath);
            exit;
        }
    }
    return false;
}

require_once $publicPath.'/index.php';
