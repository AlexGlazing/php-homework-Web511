<h2>Вход</h2>
<?php if (!empty($registered)): ?>
    <p class="success-message"><?= htmlspecialchars($registered) ?></p>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form action="" method="post">
    <label>Логин</label>
    <input type="text" name="login" required autocomplete="username">

    <label>Пароль</label>
    <input type="password" name="password" required autocomplete="current-password">

    <button type="submit">Войти</button>
</form>

<p style="margin-top: 16px;">
    Нет аккаунта? <a href="/register">Зарегистрироваться</a>
</p>
