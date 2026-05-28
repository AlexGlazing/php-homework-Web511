<button
    type="button"
    class="likeBtn"
    data-id="<?= htmlspecialchars((string)$post['id']) ?>"
    aria-pressed="<?= !empty($post['liked']) ? 'true' : 'false' ?>"
    style="cursor:pointer"
>
    <span class="likeIcon"><?= !empty($post['liked']) ? '♥' : '♡' ?></span>
    <span class="likeCount"><?= (int)($post['like_count'] ?? 0) ?></span>
</button>
