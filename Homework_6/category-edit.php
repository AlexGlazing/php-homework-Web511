<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategoryById;
use function CompanyName\Blog\saveCategory;
use function CompanyName\Blog\redirectToError;

try {
    $name = '';
    $slug = '';
    $description = '';
    $errors = [];
    $category = [];
    $id = null;

    
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $category = getCategoryById($id);
        $name = $category['name'] ?? '';
        $slug = $category['slug'] ?? '';
        $description = $category['description'] ?? '';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
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
         
            saveCategory([
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
            ]);
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
    <title>Редактировать категорию</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php'; ?>
<h2>Редактировать категорию</h2>
<form action="" method="post">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <label>Название:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>">
    </label>
    <?php if (!empty($errors['name'])): ?>
        <p style="color:red;"><?= $errors['name'] ?></p>
    <?php endif; ?><br>
    <label>Slug:<br>
        <input type="text" name="slug" value="<?= htmlspecialchars($slug) ?>">
    </label>
    <?php if (!empty($errors['slug'])): ?>
        <p style="color:red;"><?= $errors['slug'] ?></p>
    <?php endif; ?><br>
    <label>Описание:<br>
        <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
    </label>
    <?php if (!empty($errors['description'])): ?>
        <p style="color:red;"><?= $errors['description'] ?></p>
    <?php endif; ?><br>
    <input type="submit" value="Сохранить">
</form>
</body>
</html>
