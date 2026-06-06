<?php
/**
 * Front controller for the application.
 * All requests are routed through this file.
 * Static files (non-PHP) are served directly from the public directory.
 * All other requests are handled by the application (public/index.php).
 *
 * Start the dev server from the project root like this:
 *   php -S 127.0.0.1:8000 router.php
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestUri = strtok($requestUri, '?') ?: '/';

// Normalize the requested path (strip leading slash for joining)
$relative = ltrim($requestUri, '/\\');

// Build a safe path inside /public
$publicDir = __DIR__ . DIRECTORY_SEPARATOR . 'public';
$fullPath = $publicDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);

// Basic path traversal protection
$realPublic = realpath($publicDir);
$realFile = is_file($fullPath) ? realpath($fullPath) : false;

if ($realFile && $realPublic && str_starts_with($realFile, $realPublic)) {
    $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

    if ($extension !== 'php') {
        // MIME type resolution that does NOT require the fileinfo extension.
        // Many PHP CLI builds (especially on Windows) do not have ext-fileinfo enabled.
        // We use a reliable extension map (covers everything this blog serves).
        // If mime_content_type() happens to be available, we prefer its result for accuracy.
        $mimeMap = [
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            'mjs'   => 'application/javascript',
            'json'  => 'application/json',
            'png'   => 'image/png',
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/jpeg',
            'gif'   => 'image/gif',
            'webp'  => 'image/webp',
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'woff'  => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf'   => 'font/ttf',
            'eot'   => 'application/vnd.ms-fontobject',
            'otf'   => 'font/otf',
            'txt'   => 'text/plain',
            'html'  => 'text/html',
            'htm'   => 'text/html',
        ];

        $mimeType = $mimeMap[$extension] ?? null;

        if ($mimeType === null && function_exists('mime_content_type')) {
            // Only call it when the function actually exists (prevents fatal error).
            $detected = @mime_content_type($fullPath);
            if ($detected) {
                $mimeType = $detected;
            }
        }

        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }

        header('Content-Type: ' . $mimeType);
        // Simple cache hint for development
        header('Cache-Control: public, max-age=3600');
        readfile($fullPath);
        exit;
    }
    // .php files inside public/ fall through to the app (rare)
}

// Everything else (dynamic routes, POST, etc.) goes to the application front controller.
require __DIR__ . '/index.php';
