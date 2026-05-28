<h2>Посты</h2>
<?php if (isset($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <a href="/?page=posts-category&category=<?= htmlspecialchars($category['slug']) ?>">
            <?= htmlspecialchars($category['name']) ?><br>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>
