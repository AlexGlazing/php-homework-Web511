<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\render;
use function CompanyName\Blog\Models\enrichPostsWithLikes;
use function CompanyName\Blog\Models\getCategories;
use function CompanyName\Blog\Models\getCategoryBySlug;
use function CompanyName\Blog\Models\getCurrentUser;
use function CompanyName\Blog\Models\getPostsCategoriesBySlug;

function categoryController(string $action, array $params = []): void
{
    if ($action === 'bySlug') {
        categoryBySlugAction($params);
        return;
    }

    echo render('categories', [
        'categories' => getCategories(),
        'titleSite' => 'Категории',
    ]);
}

function categoryBySlugAction(array $params): void
{
    $slug = $params['slug'] ?? null;

    if ($slug === null || $slug === '') {
        throw new \OutOfBoundsException('Slug категории не передан');
    }

    $category = getCategoryBySlug($slug);
    $posts = enrichPostsWithLikes(getPostsCategoriesBySlug($slug));

    echo render('posts/posts-category', [
        'posts' => $posts,
        'category' => $category,
        'currentUser' => getCurrentUser(),
        'titleSite' => 'Категория: ' . $category['name'],
    ]);
}
