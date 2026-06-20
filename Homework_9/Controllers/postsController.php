<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\deletePostImage;
use function CompanyName\Blog\getSuccessMessage;
use function CompanyName\Blog\render;
use function CompanyName\Blog\requireLogin;
use function CompanyName\Blog\requirePostOwner;
use function CompanyName\Blog\uploadImage;
use function CompanyName\Blog\validatePostInput;
use function CompanyName\Blog\Models\createPost;
use function CompanyName\Blog\Models\deletePost;
use function CompanyName\Blog\Models\enrichPostsWithLikes;
use function CompanyName\Blog\Models\getCategories;
use function CompanyName\Blog\Models\getPost;
use function CompanyName\Blog\Models\getPosts;

function postsController(string $action, array $params = []): void
{
    switch ($action) {
        case 'create':
            postsCreateAction();
            return;
        case 'delete':
            postsDeleteAction($params);
            return;
        default:
            postsIndexAction();
    }
}

function postsIndexAction(): void
{
    $posts = enrichPostsWithLikes(getPosts());

    echo render('posts/index', [
        'posts' => $posts,
        'success' => getSuccessMessage(),
        'currentUser' => \CompanyName\Blog\Models\getCurrentUser(),
        'titleSite' => 'Посты',
    ]);
}

function postsCreateAction(): void
{
    $user = requireLogin('/posts/create');
    $categories = getCategories();
    $categoryId = null;
    $title = '';
    $content = '';
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $validated = validatePostInput($errors);
        $title = $validated['title'];
        $content = $validated['content'];
        $categoryId = $validated['category_id'];
        $image = uploadImage($errors);

        if (empty($errors)) {
            $newId = createPost([
                'category_id' => $categoryId,
                'user_id' => $user['id'],
                'title' => $title,
                'content' => $content,
                'date' => date('Y-m-d H:i'),
                'author' => $user['nickname'],
                'image' => $image,
            ]);

            header("Location: /post/{$newId}?success=ok");
            exit();
        }
    }

    echo render('posts/create', [
        'categories' => $categories,
        'title' => $title,
        'category_id' => $categoryId,
        'content' => $content,
        'errors' => $errors,
        'titleSite' => 'Создать пост',
    ]);
}

function postsDeleteAction(array $params): void
{
    requireLogin('/posts');
    $id = (int)($params['id'] ?? 0);

    if ($id <= 0) {
        throw new \OutOfBoundsException('ID поста не передан');
    }

    $post = getPost($id);
    requirePostOwner($post);

    deletePostImage($post['image'] ?? null);
    deletePost($id);

    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    header('Location: /posts?success=delete');
    exit();
}
