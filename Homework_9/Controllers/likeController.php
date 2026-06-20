<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\Models\getPost;
use function CompanyName\Blog\Models\toggleLike;

function likeController(string $action, array $params = []): void
{
    if ($action !== 'toggle') {
        throw new \OutOfBoundsException('Неизвестное действие');
    }

    $postId = (int)($params['id'] ?? 0);

    if ($postId <= 0) {
        throw new \OutOfBoundsException('ID поста не передан');
    }

    getPost($postId);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \OutOfBoundsException('Метод не поддерживается');
    }

    $result = toggleLike($postId);

    header('Content-Type: application/json');
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit();
}
