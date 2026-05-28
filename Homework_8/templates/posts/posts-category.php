<h2>Посты категории <?= htmlspecialchars($category['name'] ?? '') ?></h2>
<?php if (!isset($error)): ?>
    <?php foreach ($posts as $post): ?>
        <div>
            <h3>
                <a href="/?page=post&id=<?= htmlspecialchars($post['id']) ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h3>
            <p><?= htmlspecialchars($post['date']) ?></p>
            <p><?= htmlspecialchars($post['author']) ?></p>
            <?php include __DIR__ . '/../components/like-button.php'; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <?= htmlspecialchars($error) ?>
<?php endif; ?>
<?php include __DIR__ . '/../components/likes-script.php'; ?>
