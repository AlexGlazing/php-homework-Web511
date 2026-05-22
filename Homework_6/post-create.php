<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\savePost;

try {
    $categories = getCategories();
    $category_id = null;
    //C - Create
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? null);
        $date = htmlspecialchars($_POST['date'] ?? '');
        $author = htmlspecialchars($_POST['author'] ?? '');

        $errors = [];
        // Валидация дополнительныx полей
        if (empty($date)) {
            $errors['date'] = 'Заполните поле даты';
        }
        if (empty($author)) {
            $errors['author'] = 'Заполните поле автора';
        }

        //Валидация
        if (empty($title)) {
            $errors['title'] = 'Заполните поле title';
        }

        if (empty($content)) {
            $errors['content'] = 'Заполните поле text';
        }

        if (empty($errors)) {
            // Формируем массив поста без ID
            $newPost = [
                 'category_id' => $category_id,
                 'title' => $title,
                 'content' => $content,
                 'date' => $date,
                 'author' => $author
            ];
            // Сохраняем пост с помощью функции модели
            savePost($newPost);
        
            // Получаем ID нового поста (он будет последним элементом после сохранения)
            $posts = getPosts();
            $lastKey = array_key_last($posts);
        
            header("Location: /post.php?id=$lastKey&success=ok");
            die();
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/components/menu.php' ?>
<h2>Cоздать пост</h2>
<form action="" method="post" enctype="application/x-www-form-urlencoded">
    Категория:<br>
    Дата:<br>
    <input type="date" name="date" value="<?= $date ?? '' ?>">
    <?php if (!empty($errors['date'])): ?>
        <p style="color:red"><?= $errors['date'] ?></p>
    <?php endif; ?>
    <br>
    Автор:<br>
    <input type="text" name="author" value="<?= $author ?? '' ?>">
    <?php if (!empty($errors['author'])): ?>
        <p style="color:red"><?= $errors['author'] ?></p>
    <?php endif; ?>
    <br>
    <select name="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] === $category_id) ? 'selected' : '' ?>
                    value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

    Заголовок поста:<br>
    <input type="text" name="title" value="<?= $title ?? '' ?>">
    <?php if (!empty($errors['title'])): ?>
        <p style="color:red"><?= $errors['title'] ?></p>
    <?php endif; ?>
    <br>
    Текст поста:<br>
    <textarea name="content"><?= $content ?? '' ?></textarea>
    <?php if (!empty($errors['content'])): ?>
        <p style="color:red"><?= $errors['content'] ?></p>
    <?php endif; ?>
    <br><br>
    <input type="submit" value="Создать">

    <!--
    <input type="checkbox" name="tags[]" value="Политика">
    <input type="checkbox" name="tags[]" value="Жесть">
    <input type="checkbox" name="tags[]" value="Еда">
    -->
</form>
</body>
</html>
