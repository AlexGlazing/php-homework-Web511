<h2>Пост</h2>
<?php if (!empty($success)): ?>
    <p style="color:green"><?=$success?></p>
<?php endif; ?>
<div>
    <h3><?= htmlspecialchars($post['title']) ?></h3>
    <?php if (!empty($post['image'])): ?>
        <p><img src="/upload/<?= htmlspecialchars($post['image']) ?>" alt="" style="width: 200px"></p>
    <?php endif; ?>
    <p><?= htmlspecialchars($post['content']) ?></p>
    <div class="post-meta">
        <span class="post-date"><?= htmlspecialchars($post['date']) ?></span>
        <span class="post-author"><?= htmlspecialchars($post['author']) ?></span>
    </div>
    <p>
        <?php include __DIR__ . '/../components/like-button.php'; ?>
    </p>
</div>
<?php include __DIR__ . '/../components/likes-script.php'; ?>
