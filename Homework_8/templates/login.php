<h2>Вход</h2>
<?php if (!empty($error)): ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="" method="post">
    <label>Логин</label>
    <input type="text" name="login" required autocomplete="username">

    <label>Пароль</label>
    <input type="password" name="password" required autocomplete="current-password">

    <input type="submit" value="Войти">
</form>

<p style="margin-top: 16px; color: var(--text-muted); font-size: 0.9rem;">
    Демо-доступ: <strong>admin</strong> / <strong>123</strong>
</p>
