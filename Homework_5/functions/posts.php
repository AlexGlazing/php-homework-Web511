<?php
require_once __DIR__ . '/helpers.php';

/**
 * Возвращает массив всех постов.
 */
function getPosts(): array
{
    $postsData = readFileData('posts.json');
    return decodeData($postsData);
}

/**
 * Получает пост по его ID.
 */
function getPost(int $id): array
{
    $posts = getPosts();
    if (!isset($posts[$id])) {
        throw new OutOfBoundsException('Пост не найден');
    }
    return $posts[$id];
}
