<script>
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.likeBtn');
        if (!button) {
            return;
        }

        const postId = button.getAttribute('data-id');

        (
            async () => {
                try {
                    const response = await fetch(`/?page=like-${postId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const result = await response.json();

                    if (result.status !== 'success') {
                        console.error('Ошибка: не удалось обновить лайк');
                        return;
                    }

                    button.setAttribute('aria-pressed', result.liked ? 'true' : 'false');
                    button.querySelector('.likeIcon').textContent = result.liked ? '♥' : '♡';
                    button.querySelector('.likeCount').textContent = result.count;
                } catch (error) {
                    console.error('Ошибка:', error);
                }
            }
        )();
    });
</script>
