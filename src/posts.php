<?php
require __DIR__ . '/../functions/app.php';

try {
    $posts = getPosts();
} catch (Exception $e) {
    header("Location: /src/500.php");
    exit();
}
?>

<?php
$page_title = 'Посты';
include __DIR__ . '/../templates/header.php';
?>

<h2>Посты</h2>
<?php if (!isset($error)): ?>
    <?php foreach ($posts as $post): ?>
        <div>
            <h3>
                <a href="/src/post.php?id=<?= htmlspecialchars($post['id']) ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h3>
            <p><?= htmlspecialchars($post['date']) ?></p>
            <p><?= htmlspecialchars($post['author']) ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>