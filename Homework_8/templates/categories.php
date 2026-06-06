<h2>Категории</h2>
<div class="categories-grid">
<?php if (isset($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <a class="category-chip" href="/?page=posts-category&category=<?= htmlspecialchars($category['slug']) ?>">
            <span class="chip-icon">🏷️</span>
            <span class="chip-name"><?= htmlspecialchars($category['name']) ?></span>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>
</div>
