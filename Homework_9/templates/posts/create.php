<h2>Создать пост</h2>

<form action="/posts/create" method="post" enctype="multipart/form-data">
    <label for="category_id">Категория</label>
    <select name="category_id" id="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] === $category_id) ? 'selected' : '' ?>
                    value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="title">Заголовок поста</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($title ?? '') ?>">
    <?php if (!empty($errors['title'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['title']) ?></p>
    <?php endif; ?>

    <label for="content">Текст поста</label>
    <textarea name="content" id="content"><?= htmlspecialchars($content ?? '') ?></textarea>
    <?php if (!empty($errors['content'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['content']) ?></p>
    <?php endif; ?>

    <label for="image">Изображение поста</label>
    <input type="file" name="image" id="image">
    <?php if (!empty($errors['image'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['image']) ?></p>
    <?php endif; ?>

    <button type="submit">Создать</button>
</form>
