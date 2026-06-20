<?php

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestUri = strtok($requestUri, '?') ?: '/';

$relative = ltrim($requestUri, '/\\');

$publicDir = __DIR__ . DIRECTORY_SEPARATOR . 'public';
$fullPath = $publicDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);

$realPublic = realpath($publicDir);
$realFile = is_file($fullPath) ? realpath($fullPath) : false;

if ($realFile && $realPublic && str_starts_with($realFile, $realPublic)) {
    $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

    if ($extension !== 'php') {
        $mimeMap = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'mjs' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'otf' => 'font/otf',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
        ];

        $mimeType = $mimeMap[$extension] ?? null;

        if ($mimeType === null && function_exists('mime_content_type')) {
            $detected = @mime_content_type($fullPath);
            if ($detected) {
                $mimeType = $detected;
            }
        }

        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }

        header('Content-Type: ' . $mimeType);
        header('Cache-Control: public, max-age=3600');
        readfile($fullPath);
        exit;
    }
}

require __DIR__ . '/public/index.php';
