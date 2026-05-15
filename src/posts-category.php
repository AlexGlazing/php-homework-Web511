<?php
require __DIR__ . '/../functions/app.php';

$slug = $_GET['category'] ?? null;

try {
    if (is_null($slug)) {
        throw new OutOfBoundsException('Slug категории не передан');
    }

    $category = getCategoryBySlug($slug);
    $posts = getPostsCategoriesBySlug($slug);

} catch (OutOfBoundsException $e) {
    
    http_response_code(404);
    header("Location: /src/404.php");
    exit();

} catch (Exception $e) {

    header("Location: /src/500.php");
    exit();
}
?>

<?php
$page_title = $category['name'] ?? 'Категория';
include __DIR__ . '/../templates/header.php';
?>
<h2>Посты категории <?= htmlspecialchars($category['name'] ?? '') ?></h2>
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