<?php

namespace CompanyName\Blog;

function render(string $page, array $params = []): string
{
    return renderTemplate('layouts/main', [
        'menu' => renderTemplate('components/menu', $params),
        'content' => renderTemplate($page, $params),
        'titleSite' => $params['titleSite'] ?? 'Главная',
    ]);
}

function renderTemplate(string $page, array $params = []): string
{
    extract($params, EXTR_SKIP);

    $fileName = ROOT_PATH . '/templates/' . $page . '.php';

    ob_start();

    if (file_exists($fileName)) {
        include $fileName;
    } else {
        throw new \OutOfBoundsException("Страницы {$page} не существует.");
    }

    return ob_get_clean();
}

function redirectToError(int $code, ?string $message = null, ?string $errorId = null): never
{
    $params = ['code' => $code];

    if ($message !== null) {
        $params['message'] = urlencode($message);
    }

    if ($errorId !== null) {
        $params['id'] = urlencode($errorId);
    }

    $queryString = http_build_query($params);
    header("Location: /error-handler?{$queryString}");
    exit();
}

function getSuccessMessage(): ?string
{
    $success = $_GET['success'] ?? null;

    return $success !== null ? (STATUSES[$success] ?? null) : null;
}

function postImageUrl(?string $image): ?string
{
    if ($image === null || $image === '') {
        return null;
    }

    $filename = basename(str_replace('\\', '/', $image));

    return '/upload/' . $filename;
}

function deletePostImage(?string $image): void
{
    if ($image === null || $image === '') {
        return;
    }

    $filename = basename(str_replace('\\', '/', $image));
    $path = UPLOAD_PATH . '/' . $filename;

    if (is_file($path)) {
        unlink($path);
    }
}

function currentPath(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    return rtrim($path, '/') ?: '/';
}

function isActivePath(string $path): bool
{
    $current = currentPath();

    if ($path === '/') {
        return $current === '/';
    }

    return $current === $path || str_starts_with($current, $path . '/');
}
