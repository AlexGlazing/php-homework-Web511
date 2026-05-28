<?php

namespace CompanyName\Blog;

/** @var array<string, list<string>>|null */
$likesMemory = null;

function loadLikes(): array
{
    global $likesMemory;

    if ($likesMemory !== null) {
        return $likesMemory;
    }

    $filePath = dirname(__DIR__) . '/data/likes.json';

    if (!file_exists($filePath)) {
        $likesMemory = [];
        return $likesMemory;
    }

    $fileData = file_get_contents($filePath);

    if ($fileData === false || $fileData === '') {
        $likesMemory = [];
        return $likesMemory;
    }

    $likesMemory = decodeData($fileData);

    return $likesMemory;
}

function saveLikes(array $likes): void
{
    global $likesMemory;

    $likesMemory = $likes;

    $filePath = dirname(__DIR__) . '/data/likes.json';

    if (file_put_contents($filePath, json_encode($likes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) === false) {
        throw new \RuntimeException('Не удалось сохранить лайки');
    }
}

function getCurrentUserId(): string
{
    return session_id();
}

function getLikeCount(int|string $postId): int
{
    $likes = loadLikes();
    $key = (string)$postId;

    return isset($likes[$key]) ? count($likes[$key]) : 0;
}

function isLiked(int|string $postId): bool
{
    $likes = loadLikes();
    $key = (string)$postId;
    $userId = getCurrentUserId();

    return isset($likes[$key]) && in_array($userId, $likes[$key], true);
}

function toggleLike(int|string $postId): array
{
    $likes = loadLikes();
    $key = (string)$postId;
    $userId = getCurrentUserId();

    if (!isset($likes[$key])) {
        $likes[$key] = [];
    }

    $index = array_search($userId, $likes[$key], true);

    if ($index !== false) {
        array_splice($likes[$key], $index, 1);
        $liked = false;
    } else {
        $likes[$key][] = $userId;
        $liked = true;
    }

    if (empty($likes[$key])) {
        unset($likes[$key]);
    }

    saveLikes($likes);

    return [
        'status' => 'success',
        'liked' => $liked,
        'count' => getLikeCount($postId),
    ];
}

function enrichPostsWithLikes(array $posts): array
{
    return array_map(static function (array $post): array {
        $post['like_count'] = getLikeCount($post['id']);
        $post['liked'] = isLiked($post['id']);

        return $post;
    }, $posts);
}

function enrichPostWithLikes(array $post): array
{
    $post['like_count'] = getLikeCount($post['id']);
    $post['liked'] = isLiked($post['id']);

    return $post;
}
