<h2>Cоздать пост</h2>
<form action="" method="post" enctype="multipart/form-data">
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
    <br>
    <input type="file" name="image"><br>
    <?php if (!empty($errors['image'])):?>
        <p style="color:red"><?=$errors['image']?></p>
    <?php endif;?>

    <br><br>
    <input type="submit" value="Создать">

    <!--
    <input type="checkbox" name="tags[]" value="Политика">
    <input type="checkbox" name="tags[]" value="Жесть">
    <input type="checkbox" name="tags[]" value="Еда">
    -->
</form>
