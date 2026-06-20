<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\render;
use function CompanyName\Blog\Models\authenticateUser;
use function CompanyName\Blog\Models\createUser;
use function CompanyName\Blog\Models\emailExists;
use function CompanyName\Blog\Models\getCurrentUser;
use function CompanyName\Blog\Models\nicknameExists;

function authController(string $action, array $params = []): void
{
    switch ($action) {
        case 'register':
            authRegisterAction();
            return;
        case 'logout':
            authLogoutAction();
            return;
        default:
            authLoginAction();
    }
}

function authLoginAction(): void
{
    $currentUser = getCurrentUser();
    if ($currentUser !== null) {
        header('Location: /');
        exit();
    }

    $error = null;
    $registered = isset($_GET['registered']) && $_GET['registered'] === '1'
        ? 'Регистрация успешна! Теперь вы можете войти.'
        : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $authenticated = authenticateUser($login, $password);
        if ($authenticated !== null) {
            $_SESSION['user'] = $authenticated;
            $redirect = $_GET['redirect'] ?? '/';
            header('Location: ' . $redirect);
            exit();
        }

        $error = 'Неверный логин или пароль';
    }

    echo render('login', [
        'error' => $error,
        'registered' => $registered,
        'titleSite' => 'Вход',
    ]);
}

function authRegisterAction(): void
{
    $errors = [];
    $nickname = '';
    $email = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($nickname === '') {
            $errors['nickname'] = 'Введите никнейм';
        } elseif (nicknameExists($nickname)) {
            $errors['nickname'] = 'Такой никнейм уже занят';
        }

        if ($email === '') {
            $errors['email'] = 'Введите email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некорректный email';
        } elseif (emailExists($email)) {
            $errors['email'] = 'Этот email уже зарегистрирован';
        }

        if ($password === '') {
            $errors['password'] = 'Введите пароль';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Пароль должен быть не короче 6 символов';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Пароли не совпадают';
        }

        if (empty($errors)) {
            createUser($nickname, $email, $password);
            header('Location: /login?registered=1');
            exit();
        }

        $nickname = htmlspecialchars($nickname);
        $email = htmlspecialchars($email);
    }

    echo render('register', [
        'nickname' => $nickname,
        'email' => $email,
        'errors' => $errors,
        'titleSite' => 'Регистрация',
    ]);
}

function authLogoutAction(): void
{
    unset($_SESSION['user']);
    header('Location: /');
    exit();
}
