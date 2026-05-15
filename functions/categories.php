<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/posts.php';

/**
 * Возвращает список всех категорий.
 */
function getCategories(): array
{
    $categoriesData = readFileData('categories.json');
    return decodeData($categoriesData);
}

/**
 * Получает категорию по её slug.
 */
function getCategoryBySlug(string $slug): array
{
    $categories = getCategories();
    $filtered = array_filter($categories, fn($cat) => $cat['slug'] === $slug);

    if (empty($filtered)) {
        throw new OutOfBoundsException("Категория с slug '{$slug}' не найдена");
    }

    return array_values($filtered)[0];
}

/**
 * Получает категорию по её ID.
 */
function getCategoryById(int $id): array
{
    $categories = getCategories();
    foreach ($categories as $cat) {
        if (isset($cat['id']) && $cat['id'] === $id) {
            return $cat;
        }
    }
    throw new OutOfBoundsException('Категория не найдена');
}

/**
 * Возвращает посты, принадлежащие категории с заданным slug.
 */
function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);
    return getPostsCategoriesById($category['id']);
}

/**
 * Возвращает посты, принадлежащие категории по ID.
 */
function getPostsCategoriesById(int $id): array
{
    $posts = getPosts();
    $filteredPosts = array_filter($posts, function ($post) use ($id) {
        return isset($post['category_id']) && $post['category_id'] === $id;
    });
    return array_values($filteredPosts);
}
