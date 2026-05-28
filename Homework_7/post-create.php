<?php
require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\getPosts;


$categories = getCategories();
$category_id = null;
//C - Create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = htmlspecialchars($_POST['title'] ?? '');
    $content = htmlspecialchars($_POST['content'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? null);

    $errors = [];

    //Валидация
    if (empty($title)) {
        $errors['title'] = 'Заполните поле title';
    }

    if (empty($content)) {
        $errors['content'] = 'Заполните поле text';
    }

    if (empty($errors)) {
        $posts = getPosts();

        $posts[] = [
            'category_id' => $category_id,
            'title' => $title,
            'content' => $content,
            'date' => date('Y-m-d H:i'),
            'author' => 'Guest'

        ];

        $lastKey = array_key_last($posts);
        $posts[$lastKey]['id'] = $lastKey;

        $posts[$lastKey] = array_merge(['id' => $lastKey], $posts[$lastKey]);

        // savePost($post);
        file_put_contents(__DIR__ . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        header("Location: /post.php?id=$lastKey&success=ok");
        die();
    }

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
