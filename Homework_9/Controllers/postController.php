<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\deletePostImage;
use function CompanyName\Blog\getSuccessMessage;
use function CompanyName\Blog\render;
use function CompanyName\Blog\requireLogin;
use function CompanyName\Blog\requirePostOwner;
use function CompanyName\Blog\uploadImage;
use function CompanyName\Blog\validatePostInput;
use function CompanyName\Blog\Models\enrichPostWithLikes;
use function CompanyName\Blog\Models\getCategories;
use function CompanyName\Blog\Models\getPost;
use function CompanyName\Blog\Models\updatePost;

function postController(string $action, array $params = []): void
{
    switch ($action) {
        case 'edit':
            postEditAction($params);
            return;
        case 'save':
            postSaveAction($params);
            return;
        default:
            postShowAction($params);
    }
}

function postShowAction(array $params): void
{
    $id = (int)($params['id'] ?? 0);

    if ($id <= 0) {
        throw new \OutOfBoundsException('ID поста не передан');
    }

    $post = enrichPostWithLikes(getPost($id));

    echo render('posts/show', [
        'post' => $post,
        'success' => getSuccessMessage(),
        'titleSite' => $post['title'],
    ]);
}

function postEditAction(array $params): void
{
    requireLogin();
    $id = (int)($params['id'] ?? 0);

    if ($id <= 0) {
        throw new \OutOfBoundsException('ID поста не передан');
    }

    $post = getPost($id);
    requirePostOwner($post);

    echo render('posts/edit', [
        'post' => $post,
        'categories' => getCategories(),
        'title' => '',
        'category_id' => null,
        'content' => '',
        'errors' => [],
        'id' => $id,
        'titleSite' => 'Редактировать пост',
    ]);
}

function postSaveAction(array $params): void
{
    requireLogin();
    $id = (int)($params['id'] ?? $_POST['id'] ?? 0);

    if ($id <= 0) {
        throw new \OutOfBoundsException('ID поста не передан');
    }

    $post = getPost($id);
    requirePostOwner($post);

    $errors = [];
    $validated = validatePostInput($errors);
    $image = uploadImage($errors);

    if (!empty($errors)) {
        echo render('posts/edit', [
            'post' => $post,
            'categories' => getCategories(),
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'errors' => $errors,
            'id' => $id,
            'titleSite' => 'Редактировать пост',
        ]);

        return;
    }

    $newImage = $image ?? $post['image'];
    if ($image !== null && !empty($post['image']) && $post['image'] !== $image) {
        deletePostImage($post['image']);
    }

    updatePost([
        'id' => $id,
        'category_id' => $validated['category_id'],
        'title' => $validated['title'],
        'content' => $validated['content'],
        'image' => $newImage,
    ]);

    header("Location: /post/{$id}?success=edit");
    exit();
}
