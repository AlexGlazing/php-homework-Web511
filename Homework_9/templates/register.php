<h2>Регистрация</h2>

<form action="" method="post">
    <label>Никнейм</label>
    <input type="text" name="nickname" value="<?= $nickname ?? '' ?>" required autocomplete="username">
    <?php if (!empty($errors['nickname'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['nickname']) ?></p>
    <?php endif; ?>

    <label>Email</label>
    <input type="email" name="email" value="<?= $email ?? '' ?>" required autocomplete="email">
    <?php if (!empty($errors['email'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
    <?php endif; ?>

    <label>Пароль</label>
    <input type="password" name="password" required autocomplete="new-password">
    <?php if (!empty($errors['password'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
    <?php endif; ?>

    <label>Подтверждение пароля</label>
    <input type="password" name="password_confirm" required autocomplete="new-password">
    <?php if (!empty($errors['password_confirm'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['password_confirm']) ?></p>
    <?php endif; ?>

    <button type="submit">Зарегистрироваться</button>
</form>

<p style="margin-top: 16px;">
    Уже есть аккаунт? <a href="/login">Войти</a>
</p>
