<?php

use function CompanyName\Blog\canManagePost;
?>
<h2>Посты категории <?= htmlspecialchars($category['name'] ?? '') ?></h2>
<?php foreach ($posts as $post): ?>
    <div id="<?=$post['id']?>">
        <h3>
            <a href="/post/<?= htmlspecialchars($post['id']) ?>">
                <?= htmlspecialchars($post['title']) ?>
            </a>
            <?php if (canManagePost($post)): ?>
                <span class="admin-controls">
                    <a href="/post/<?=$post['id']?>/edit" class="editBtn">✏️ Редактировать</a>
                    <button data-id="<?=$post['id']?>" type="button" class="deleteBtn">🗑️ Удалить</button>
                </span>
            <?php endif; ?>
        </h3>
        <?php if (!empty($post['image'])): ?>
            <p><img src="<?= htmlspecialchars(\CompanyName\Blog\postImageUrl($post['image'])) ?>" alt="" style="width: 120px"></p>
        <?php endif; ?>
        <div class="post-meta">
            <span class="post-date"><?= htmlspecialchars($post['date']) ?></span>
            <span class="post-author"><?= htmlspecialchars($post['author']) ?></span>
        </div>
        <?php include __DIR__ . '/../components/like-button.php'; ?>
    </div>
<?php endforeach; ?>
<script>
    window.onload = function () {
        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.onclick = function () {
                const id = this.getAttribute('data-id');

                (
                    async () => {
                        try {
                            const response = await fetch(`/posts/delete/${id}?ajax=1`);
                            const result = await response.json();
                            switch (result.status) {
                                case 'success':
                                    const postElement = document.getElementById(id);
                                    postElement.style.transition = 'all 0.5s ease';
                                    postElement.style.opacity = '0';
                                    postElement.style.transform = 'translateY(-20px)';
                                    setTimeout(() => {
                                        postElement.remove();
                                    }, 500);
                                    break;
                                case 'error':
                                    console.error('Ошибка: не могу удалить');
                                    break;
                                default:
                                    console.error('Ошибка: не верный формат ответа');
                            }
                        } catch (error) {
                            console.error('Ошибка:', error);
                        }
                    }
                )();
            }
        });
    }
</script>
<?php include __DIR__ . '/../components/likes-script.php'; ?>
