<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\renderTemplate;

function errorHandlerController(string $action, array $params = []): void
{
    $errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
    $errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : null;
    $errorId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

    if (!array_key_exists($errorCode, ERRORS)) {
        $errorCode = 404;
    }

    $config = ERRORS[$errorCode];
    if ($errorMessage) {
        $config['message'] = htmlspecialchars($errorMessage);
    }

    http_response_code($errorCode);
    header('X-Robots-Tag: noindex, nofollow');

    echo renderTemplate('error', [
        'errorCode' => $errorCode,
        'config' => $config,
        'errorId' => $errorId,
    ]);
}
