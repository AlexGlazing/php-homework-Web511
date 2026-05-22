<?php

require __DIR__.'/vendor/autoload.php';

use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\deleteCategory;
const STATUSES = [
    'ok' => 'Категория успешно удалена',
];
$success = STATUSES[($_GET['success'] ?? null)] ?? null;

try {
    // Delete category if requested
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = $_GET['id'] ?? null;
        deleteCategory((int)$id);
        header("Location: /categories.php?success=ok");
        die();
    }

    $categories = getCategories();

} catch (Exception $e) {

    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(500);

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .category-item { margin-bottom: 12px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .category-name { font-weight: bold; }
        .edit-btn { margin-right: 10px; }
        .delete-btn { color: red; cursor: pointer; }
    </style>
</head>
<body>
<?php include __DIR__ . '/components/menu.php' ?>
<h2>Категории</h2>
<?php if (!empty($success)): ?>
    <p style="color:green"><?=$success?></p>
<?php endif; ?>
<a href="/category-create.php"><button>Создать категорию</button></a>
<?php if (isset($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <div class="category-item">
            <a href="/posts-category.php?category=<?= htmlspecialchars($category['slug']) ?>" class="category-name">
                <?= htmlspecialchars($category['name']) ?>
            </a>
            <div class="category-actions">
                <a href="/category-edit.php?action=edit&id=<?= htmlspecialchars($category['id']) ?>" class="edit-btn">Редактировать</a>
                <a href="/category-delete.php?id=<?= htmlspecialchars($category['id']) ?>" class="delete-btn" onclick="return confirm('Вы уверены, что хотите удалить категорию: <?= addslashes(htmlspecialchars($category['name'])) ?>?');">Удалить</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Ошибка загрузки категорий</p>
<?php endif; ?>

</body>
</html>