<?php

namespace CompanyName\Blog;

use function CompanyName\Blog\Models\getCurrentUser;

function requireLogin(?string $redirectTo = null): array
{
    $user = getCurrentUser();

    if ($user === null) {
        $redirect = $redirectTo ?? currentPath();
        header('Location: /login?redirect=' . urlencode($redirect));
        exit();
    }

    return $user;
}

function canManagePost(array $post): bool
{
    $user = getCurrentUser();

    return $user !== null
        && isset($post['user_id'])
        && $post['user_id'] !== null
        && (int)$post['user_id'] === (int)$user['id'];
}

function requirePostOwner(array $post): void
{
    if (!canManagePost($post)) {
        redirectToError(403);
    }
}
