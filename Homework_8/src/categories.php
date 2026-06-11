<?php
namespace CompanyName\Blog;


function getCategoryBySlug(string $slug): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM categories WHERE slug = ?');
    $stmt->execute([$slug]);
    $cat = $stmt->fetch();

    if ($cat === false) {
        throw new \OutOfBoundsException("Категория с slug '{$slug}' не найдена");
    }

    return $cat;
}

function getCategoryById(int $id): array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $cat = $stmt->fetch();

    if ($cat === false) {
        throw new \OutOfBoundsException("Категория не найдена");
    }

    return $cat;
}

function getCategories(): array
{
    $db = getDb();
    $stmt = $db->query('SELECT * FROM categories ORDER BY id');
    $rows = $stmt->fetchAll();

    $result = [];
    foreach ($rows as $row) {
        $result[$row['id']] = $row;
    }

    return $result;
}

