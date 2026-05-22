<?php
namespace CompanyName\Blog;


function getCategoryBySlug(string $slug): array
{
    $categories = getCategories();
    $filtered = array_filter($categories, fn($cat) => $cat['slug'] === $slug);

    if (empty($filtered)) {
        throw new \OutOfBoundsException("Категория с slug '{$slug}' не найдена");
    }

    return array_values($filtered)[0];
}

function getCategoryById(int $id): array
{
    $category = getCategories();

    if (!isset($category[$id])) {
        throw new \OutOfBoundsException("Категория не найдена");
    }

    return $category[$id];
}

function getCategories()
{
    $categoriesData = readFileData('categories.json');
    return decodeData($categoriesData);
}

function saveCategory(array $category): void
{
    
    $categories = getCategories();
    
    if (!isset($category['id'])) {
        $maxId = -1;
        foreach ($categories as $cat) {
            if (isset($cat['id']) && $cat['id'] > $maxId) {
                $maxId = $cat['id'];
            }
        }
        $category['id'] = $maxId + 1;
    }
    
    $categories[$category['id']] = $category;
    
    file_put_contents(dirname(__DIR__) . '/data/categories.json', json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function deleteCategory(int $id): void
{
    $categories = getCategories();
    if (!isset($categories[$id])) {
        throw new \OutOfBoundsException("Категория не найдена");
    }
    unset($categories[$id]);
    file_put_contents(dirname(__DIR__) . '/data/categories.json', json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

