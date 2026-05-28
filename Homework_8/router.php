<?php
/**
 * Front controller for the application.
 * All requests are routed through this file.
 * Static files (non-PHP) are served directly from the public directory.
 * All other requests are handled by the application (public/index.php).
 */

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove query string
$requestUri = strtok($requestUri, '?');

$publicDir = __DIR__ . '/public';
$fullPath = $publicDir . $requestUri;

// If the requested file exists in the public directory and is not a PHP file, serve it directly
if (is_file($fullPath)) {
    $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    if ($extension !== 'php') {
        // Serve static file
        $mimeType = mime_content_type($fullPath);
        if ($mimeType !== false) {
            header('Content-Type: ' . $mimeType);
        }
        readfile($fullPath);
        exit;
    }
    // If it is a PHP file, we fall through to route to the application
}

// Route to the front controller (application)
require __DIR__ . '/index.php';
