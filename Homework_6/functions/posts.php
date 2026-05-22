<?php

namespace CompanyName\Blog;

function savePost(array $post): void
{
    // Получаем текущие посты
    $posts = getPosts();
    // Вычисляем новый идентификатор
    $lastKey = array_key_last($posts);
    $newId = $lastKey !== null ? ((int)$lastKey) + 1 : 1;
    // Добавляем ID к посту
    $post['id'] = $newId;
    // Сохраняем пост в массив
    $posts[$newId] = $post;
    // Записываем обновлённый массив в файл
    file_put_contents(dirname(__DIR__) . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function getPost(int $id): array
{
    $posts = getPosts();

    if (!isset($posts[$id])) {
        throw new \OutOfBoundsException("Пост не найден");
    }

    return $posts[$id];
}

function getPosts(): array
{
    $postsData = readFileData('posts.json');
    return decodeData($postsData);
}


function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);

    return getPostsCategoriesById($category['id']);
}


function getPostsCategoriesById(int $id): array
{
    $posts = getPosts();

    $filteredPosts = array_filter($posts, function ($post) use ($id) {
        return isset($post['category_id']) && $post['category_id'] === $id;
    });

    return array_values($filteredPosts);
}

/**
 * Удалить пост по идентификатору.
 */
function deletePost(int $id): void
{
    $posts = getPosts();
    if (!isset($posts[$id])) {
        throw new \OutOfBoundsException("Пост не найден");
    }
    unset($posts[$id]);
    file_put_contents(dirname(__DIR__) . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

