<?php
require __DIR__.'/vendor/autoload.php';
/*require __DIR__ . '/functions/config.php';
require __DIR__ . '/functions/categories.php';
require __DIR__ . '/functions/posts.php';*/

use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\deletePost;

const STATUSES = [
    'ok' => 'Пост успешно удален',
];
$success = STATUSES[($_GET['success'] ?? null)] ?? null;

try {

    $posts = getPosts();
    // Сортируем посты по ID в обратном порядке, чтобы новые посты отображались сверху
    krsort($posts);

    //D - Delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        $id = $_GET['id'] ?? null;
        // вызов модели для удаления поста
        deletePost((int)$id);
        header("Location: /posts.php?success=ok");
        die();
    }



} catch (Exception $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();

    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //Редирект
    redirectToError(500, $e->getMessage(), $errorId);

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php' ?>
<a href="/post-create.php"> <button>Создать пост</button></a>
<h2>Посты</h2>
<?php if (!empty($success)): ?>
    <p style="color:green"><?=$success?></p>
<?php endif; ?>

<?php if (!isset($error)): ?>
    <?php foreach ($posts as $post): ?>
        <div>
            <h3>
                <a href="/post.php?id=<?= $post['id'] ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
                &nbsp;&nbsp;&nbsp;
                <a href="/post-edit.php?action=edit&id=<?=$post['id']?>">[edit]</a>
                &nbsp;&nbsp;&nbsp;
                <a href="/posts.php?action=delete&id=<?=$post['id']?>">[X]</a>
            </h3>
            <p><?= htmlspecialchars($post['date']) ?></p>
            <p><?= htmlspecialchars($post['author']) ?></p>
        </div>
    <?php endforeach; ?>

<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>
</body>
</html>