<h2>Добро пожаловать в Блог.</h2>
<div class="welcome">
    <p>Это учебный блог на PHP с архитектурой MVC и ЧПУ. Вы можете просматривать посты, ставить лайки, переключать тему, зарегистрироваться и управлять своими постами.</p>
    <p>
        <a href="/posts">📚 Все посты</a> &nbsp;•&nbsp;
        <a href="/categories">🏷️ Категории</a>
        <?php if (!empty($currentUser)): ?>
            &nbsp;•&nbsp; <a href="/posts/create">✍️ Написать пост</a>
        <?php else: ?>
            &nbsp;•&nbsp; <a href="/login">🔐 Войти</a>
        <?php endif; ?>
    </p>
    <p class="hint">Совет: попробуйте переключить «Тёмная тема» — выбор сохранится в куке.</p>
</div>
