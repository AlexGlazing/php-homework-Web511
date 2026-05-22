<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\deleteCategory;
use function CompanyName\Blog\redirectToError;

try {
    $id = null;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        throw new Exception('ID категории не указан');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        deleteCategory($id);
        header('Location: /categories.php');
        exit();
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
    <title>Удалить категорию</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php'; ?>
<h2>Подтвердите удаление категории</h2>
<p>Вы действительно хотите удалить категорию с ID <?= htmlspecialchars($id) ?>?</p>
<form method="post">
    <input type="submit" value="Удалить">
    <a href="/categories.php">Отмена</a>
</form>
</body>
</html>
