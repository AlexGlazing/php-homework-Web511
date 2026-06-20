<?php

require __DIR__ . '/../vendor/autoload.php';

use function CompanyName\Blog\redirectToError;

session_start();

$theme = $_COOKIE['theme'] ?? 'light';
if (!in_array($theme, ['light', 'dark'], true)) {
    $theme = 'light';
}
$_COOKIE['theme'] = $theme;

if (empty($_COOKIE['uid'])) {
    $uid = bin2hex(random_bytes(16));
    setcookie('uid', $uid, time() + 3600 * 24 * 365, '/');
    $_COOKIE['uid'] = $uid;
}

function matchRoute(string $path, array $routes): ?array
{
    foreach ($routes as $pattern => $handler) {
        $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';

        if (preg_match($regex, $path, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            return array_merge($handler, ['params' => $params]);
        }
    }

    return null;
}

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = trim($requestPath, '/');
$routes = require ROOT_PATH . '/config/routes.php';
$route = matchRoute($path, $routes);

try {
    if ($route === null) {
        throw new OutOfBoundsException('Страница не найдена');
    }

    $controllerName = $route['controller'] . 'Controller';
    $controllerFunction = 'CompanyName\\Blog\\Controllers\\' . $controllerName;

    if (!function_exists($controllerFunction)) {
        throw new OutOfBoundsException('Нет такого контроллера страницы');
    }

    $controllerFunction($route['action'], $route['params'] ?? []);
} catch (OutOfBoundsException $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();
    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    if (DEBUG === true) {
        var_dump($errorDetails);
        exit();
    }

    redirectToError(404, $e->getMessage(), $errorId);
} catch (Exception $e) {
    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    redirectToError(500);
}
