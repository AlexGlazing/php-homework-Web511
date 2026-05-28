<h2>Править пост пост</h2>
<form action="/?page=post-edit&action=save" method="post" enctype="application/x-www-form-urlencoded">
    <input type="text" name="id" readonly hidden value="<?= $post['id'] ?? $id ?? '' ?>">
    Категория:<br>
    <select name="category_id">
            <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] == ($post['category_id'] ?? $category_id)) ? 'selected' : '' ?>
                    value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endforeach; ?>
    </select><br>

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
