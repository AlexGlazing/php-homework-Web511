<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\getPost;
use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\redirectToError;

try {
    $categories = getCategories();
    $category_id = null;
    $post = [];

    //C - Edit
    $date = '';
    $author = '';
    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
        $id = (int)$_GET['id'];
        $post = getPost($id);
        $date = $post['date'] ?? '';
        $author = $post['author'] ?? '';
    }

    if (isset($_GET['action']) && $_GET['action'] === 'save') {
        $id = htmlspecialchars($_POST['id'] ?? '');
        $title = htmlspecialchars($_POST['title'] ?? '');
        $content = htmlspecialchars($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? null);
        $date = htmlspecialchars($_POST['date'] ?? '');
        $author = htmlspecialchars($_POST['author'] ?? '');

        //Валидация
        if (empty($title)) {
            $errors['title'] = 'Заполните поле title';
        }
        if (empty($date)) {
            $errors['date'] = 'Заполните поле даты';
        }
        if (empty($author)) {
            $errors['author'] = 'Заполните поле автора';
        }

        if (empty($content)) {
            $errors['content'] = 'Заполните поле text';
        }

        if (empty($errors)) {
            $posts = getPosts();
            $posts[$id] = [
                'id' => $id,
                'category_id' => $category_id,
                'title' => $title,
                'content' => $content,
                'date' => $date,
                'author' => $author
            ];

            file_put_contents(__DIR__ . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            header("Location: /post.php?id=$id&success=edit");
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
<h2>Править пост "<?= $post['title'] ?>"</h2>
<form action="/post-edit.php?action=save" method="post" enctype="application/x-www-form-urlencoded">
    <input type="text" name="id" readonly hidden value="<?= $post['id'] ?? '' ?>">
    Категория:<br>
    <select name="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] == ($post['category_id'] ?? $category_id)) ? 'selected' : '' ?>
                value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endforeach; ?>
    </select><br>
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

    Заголовок поста:<br>
    <input type="text" name="title" value="<?= $post['title'] ?? $title ?? '' ?>">
    <?php if (!empty($errors['title'])): ?>
        <p style="color:red"><?= $errors['title'] ?></p>
    <?php endif; ?>
    <br>
    Текст поста:<br>
    <textarea name="content"><?= $post['content'] ?? $content ?? '' ?></textarea>
    <?php if (!empty($errors['content'])): ?>
        <p style="color:red"><?= $errors['content'] ?></p>
    <?php endif; ?>
    <br><br>
    <input type="submit" value="Изменить">
</form>
</body>
</html>
