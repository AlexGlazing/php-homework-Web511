<?php
require __DIR__ . '/../functions/app.php';

try {
    $id = $_GET['id'] ?? null;

    //Валидация
    if (is_null($id)) {
        throw new OutOfBoundsException('ID поста не передан');
    }

    if (!is_numeric($id)) {
        throw new OutOfBoundsException('ID поста должен быть числом');
    }


    $post = getPost($id);


} catch (OutOfBoundsException $e) {

   header("Location: /src/404.php");
   exit();

} catch (Exception $e) {

    header("Location: /src/500.php");
    exit();
}
?>

<?php
$page_title = $post['title'] ?? 'Пост';
include __DIR__ . '/../templates/header.php';
?>
<h2>Пост</h2>
<?php if (!isset($error)): ?>
    <div>
        <h3><?= htmlspecialchars($post['title']) ?></h3>
        <p><?= htmlspecialchars($post['content']) ?></p>
        <span><?= htmlspecialchars($post['date']) ?></span>
        <span><?= htmlspecialchars($post['author']) ?></span>
    </div>
<?php else: ?>
    <p>Ошибка загрузки категорий.</p>
<?php endif; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>