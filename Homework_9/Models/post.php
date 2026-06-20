<?php

namespace CompanyName\Blog\Models;

use function CompanyName\Blog\getDb;

function updatePost(array $post): void
{
    $db = getDb();
    $stmt = $db->prepare(
        'UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?'
    );
    $success = $stmt->execute([
        $post['title'],
        $post['content'],
        $post['category_id'],
        $post['image'] ?? null,
        $post['id'],
    ]);

    if (!$success) {
        throw new \Exception('Не удалось сохранить данные');
    }
}

function getPost(int $id): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if ($post === false) {
        throw new \OutOfBoundsException('Пост не найден');
    }

    return $post;
}

function getPosts(): array
{
    $db = getDb();
    $stmt = $db->query('SELECT * FROM posts ORDER BY id');

    return $stmt->fetchAll();
}

function getPostsCategoriesBySlug(string $slug): array
{
    $category = getCategoryBySlug($slug);

    return getPostsCategoriesById((int)$category['id']);
}

function getPostsCategoriesById(int $id): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM posts WHERE category_id = ? ORDER BY id');
    $stmt->execute([$id]);

    return $stmt->fetchAll();
}

function createPost(array $data): int
{
    $db = getDb();
    $stmt = $db->prepare(
        'INSERT INTO posts (category_id, user_id, title, content, date, author, image) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $success = $stmt->execute([
        $data['category_id'] ?? null,
        $data['user_id'] ?? null,
        $data['title'],
        $data['content'],
        $data['date'],
        $data['author'],
        $data['image'] ?? null,
    ]);

    if (!$success) {
        throw new \Exception('Не удалось создать пост');
    }

    return (int)$db->lastInsertId();
}

function deletePost(int $id): void
{
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
}
