<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\saveCategory;
use function CompanyName\Blog\redirectToError;

try {
    // Initialize variables
    $name = '';
    $slug = '';
    $description = '';
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $errors['name'] = 'Заполните поле названия';
        }
        if (empty($slug)) {
            $errors['slug'] = 'Заполните поле slug';
        }
        if (empty($description)) {
            $errors['description'] = 'Заполните поле описания';
        }

        if (empty($errors)) {
            // Сохранить новую категорию
            saveCategory([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ]);
            // Перенаправить к списку категорий
            header('Location: /categories.php');
            exit();
        }
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
    redirectToError(500, $e->getMessage(), $errorId);
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать категорию</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php'; ?>
<h2>Создать категорию</h2>
<form action="" method="post">
    <label>Название:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
    </label>
    <?php if (!empty($errors['name'])): ?>
        <p style="color:red;"><?= $errors['name'] ?></p>
    <?php endif; ?>
    <br>
    <label>Slug:<br>
        <input type="text" name="slug" value="<?= htmlspecialchars($slug) ?>">
    </label>
    <?php if (!empty($errors['slug'])): ?>
        <p style="color:red;"><?= $errors['slug'] ?></p>
    <?php endif; ?>
    <br>
    <label>Описание:<br>
        <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
    </label>
    <?php if (!empty($errors['description'])): ?>
        <p style="color:red;"><?= $errors['description'] ?></p>
    <?php endif; ?>
    <br>
    <input type="submit" value="Создать">
</form>
</body>
</html>
