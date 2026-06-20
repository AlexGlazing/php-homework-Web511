<?php

use function CompanyName\Blog\postImageUrl;
?>
<h2>Редактировать пост</h2>

<form action="/post/<?= (int)($post['id'] ?? $id ?? 0) ?>/save" method="post" enctype="multipart/form-data">
    <input type="text" name="id" readonly hidden value="<?= htmlspecialchars((string)($post['id'] ?? $id ?? '')) ?>">

    <label for="category_id">Категория</label>
    <select name="category_id" id="category_id">
        <?php foreach ($categories as $category): ?>
            <option <?= ($category['id'] == ($post['category_id'] ?? $category_id)) ? 'selected' : '' ?>
                    value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="title">Заголовок поста</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title'] ?? $title ?? '') ?>">
    <?php if (!empty($errors['title'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['title']) ?></p>
    <?php endif; ?>

    <label for="content">Текст поста</label>
    <textarea name="content" id="content"><?= htmlspecialchars($post['content'] ?? $content ?? '') ?></textarea>
    <?php if (!empty($errors['content'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['content']) ?></p>
    <?php endif; ?>

    <?php if (!empty($post['image'])): ?>
        <p>Текущее изображение:</p>
        <p><img src="<?= htmlspecialchars(postImageUrl($post['image'])) ?>" alt="" style="width: 120px"></p>
    <?php endif; ?>

    <label for="image">Новое изображение (необязательно)</label>
    <input type="file" name="image" id="image">
    <?php if (!empty($errors['image'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['image']) ?></p>
    <?php endif; ?>

    <button type="submit">Изменить</button>
</form>
