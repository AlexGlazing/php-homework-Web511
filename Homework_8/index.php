<?php

require __DIR__ . '/vendor/autoload.php';

use function CompanyName\Blog\enrichPostWithLikes;
use function CompanyName\Blog\enrichPostsWithLikes;
use function CompanyName\Blog\getCategories;
use function CompanyName\Blog\getPost;
use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\getCategoryBySlug;
use function CompanyName\Blog\getPostsCategoriesBySlug;
use function CompanyName\Blog\render;
use function CompanyName\Blog\render_template;
use function CompanyName\Blog\redirectToError;
use function CompanyName\Blog\toggleLike;
use function CompanyName\Blog\updatePost;
use function CompanyName\Blog\createPost;
use function CompanyName\Blog\deletePost;
use function CompanyName\Blog\createUser;
use function CompanyName\Blog\emailExists;
use function CompanyName\Blog\nicknameExists;
use function CompanyName\Blog\authenticateUser;
use function CompanyName\Blog\getCurrentUser;

session_start();

function isAdmin(): bool
{
    return !empty($_SESSION['is_admin']);
}

$theme = $_COOKIE['theme'] ?? 'light';
if (!in_array($theme, ['light', 'dark'], true)) {
    $theme = 'light';
}
$_COOKIE['theme'] = $theme;

// Ensure a persistent uid cookie for *anonymous* like identity (cookie-backed).
// When a user is logged in via the users table, likes use "user:<id>" instead (see src/likes.php).
// The cookie uid remains as fallback for guests and pre-login anonymous likes.
if (empty($_COOKIE['uid'])) {
    $uid = bin2hex(random_bytes(16));
    setcookie('uid', $uid, time() + 3600 * 24 * 365, '/');
    $_COOKIE['uid'] = $uid;
}



const STATUSES = [
    'ok' => 'Пост успешно создан',
    'delete' => 'Пост успешно удален',
    'edit' => 'Пост успешно изменен',
    'info' => 'Поздравляю',
];

$success = isset($_GET['success']) ? (STATUSES[$_GET['success']] ?? null) : null;

$page = (string)($_GET['page'] ?? 'index');

try {
    switch (true) {
        case str_starts_with($page, 'like-'):
            $postId = (int)substr($page, 5);

            if ($postId <= 0) {
                throw new OutOfBoundsException('ID поста не передан');
            }

            getPost($postId);

            $result = toggleLike($postId);

            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;

        case $page === 'login':
            // If already authenticated (admin or regular registered user), redirect away from login form
            if (isAdmin()) {
                header('Location: /?page=posts');
                exit;
            }
            $currentUser = getCurrentUser();
            if ($currentUser !== null) {
                header('Location: /');
                exit;
            }

            $error = null;
            $registered = isset($_GET['registered']) && $_GET['registered'] === '1'
                ? 'Регистрация успешна! Теперь вы можете войти.'
                : null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $login = trim($_POST['login'] ?? '');
                $password = $_POST['password'] ?? '';

                // 1) Hardcoded admin (for post management)
                if ($login === 'admin' && $password === '123') {
                    $_SESSION['is_admin'] = true;
                    $redirect = $_GET['redirect'] ?? '/?page=posts';
                    header('Location: ' . $redirect);
                    exit;
                }

                // 2) Registered user login (by nickname or email)
                $authenticated = authenticateUser($login, $password);
                if ($authenticated !== null) {
                    $_SESSION['user'] = $authenticated;
                    $redirect = $_GET['redirect'] ?? '/';
                    header('Location: ' . $redirect);
                    exit;
                }

                // Neither worked
                $error = 'Неверный логин или пароль';
            }

            echo render('login', [
                'error' => $error,
                'registered' => $registered,
            ]);
            break;

        case $page === 'logout':
            unset($_SESSION['is_admin']);
            unset($_SESSION['user']);
            header('Location: /');
            exit;

        case $page === 'register':
            $errors = [];
            $nickname = '';
            $email = '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nickname = trim($_POST['nickname'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $passwordConfirm = $_POST['password_confirm'] ?? '';

                if (empty($nickname)) {
                    $errors['nickname'] = 'Введите никнейм';
                } elseif (nicknameExists($nickname)) {
                    $errors['nickname'] = 'Такой никнейм уже занят';
                }

                if (empty($email)) {
                    $errors['email'] = 'Введите email';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Некорректный email';
                } elseif (emailExists($email)) {
                    $errors['email'] = 'Этот email уже зарегистрирован';
                }

                if (empty($password)) {
                    $errors['password'] = 'Введите пароль';
                } elseif (strlen($password) < 6) {
                    $errors['password'] = 'Пароль должен быть не короче 6 символов';
                }

                if ($password !== $passwordConfirm) {
                    $errors['password_confirm'] = 'Пароли не совпадают';
                }

                if (empty($errors)) {
                    createUser($nickname, $email, $password);
                    header('Location: /?page=login&registered=1');
                    exit;
                }

                // Pre-escape for safe re-display in form (matching existing pattern)
                $nickname = htmlspecialchars($nickname);
                $email = htmlspecialchars($email);
            }

            echo render('register', [
                'nickname' => $nickname,
                'email' => $email,
                'errors' => $errors,
            ]);
            break;

        case $page === 'index':
            echo render('index', ['isAdmin' => isAdmin()]);
            break;

        case $page === 'posts':
            if (isset($_GET['action']) && $_GET['action'] === 'delete') {
                if (!isAdmin()) {
                    header('Location: /?page=login');
                    exit;
                }
                $id = $_GET['id'] ?? null;
                if ($id !== null) {
                    deletePost((int)$id);
                }

                if (isset($_GET['ajax'])) {
                    $result = [
                        'status' => 'success',
                    ];

                    header('Content-Type: application/json');
                    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }

                header('Location: /?page=posts&success=delete');
                die();
            }

            $posts = enrichPostsWithLikes(getPosts());

            echo render('posts/index', [
                'posts' => $posts,
                'success' => $success,
                'isAdmin' => isAdmin(),
            ]);
            break;

        case $page === 'post':
            $id = $_GET['id'] ?? null;

            if (is_null($id)) {
                throw new OutOfBoundsException('ID поста не передан');
            }

            if (!is_numeric($id)) {
                throw new OutOfBoundsException('ID поста должен быть числом');
            }

            $post = enrichPostWithLikes(getPost((int)$id));

            echo render('posts/show', [
                'post' => $post,
                'success' => $success,
            ]);
            break;

        case $page === 'post-create':
            if (!isAdmin()) {
                header('Location: /?page=login&redirect=' . urlencode('/?page=post-create'));
                exit;
            }
            $categories = getCategories();
            $category_id = null;
            $title = '';
            $content = '';
            $errors = [];
            $safeFileName = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $extensionMimeMap = [
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                    ];
                    $maxFileSize = 5 * 1024 * 1024;
                    if ($_FILES['image']['size'] > $maxFileSize) {
                        $errors['image'] = 'Файл слишком большой';
                    }
                    $uploadDir = __DIR__ . '/public/upload/';
                    $fileName = $_FILES['image']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (!array_key_exists($fileExtension, $extensionMimeMap)) {
                        $errors['image'] = 'Не правильный тип файла';
                    }

                    $safeFileName = uniqid() . '_' . date('Y-m-d_H-i-s') . '.' . $fileExtension;

                    if (!isset($errors['image'])) {
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $safeFileName)) {
                            $errors['image'] = 'Файл не загружен';
                        }
                    }
                }

                $title = htmlspecialchars($_POST['title'] ?? '');
                $content = htmlspecialchars($_POST['content'] ?? '');
                $category_id = (int)($_POST['category_id'] ?? null);

                if (empty($title)) {
                    $errors['title'] = 'Заполните поле title';
                }

                if (empty($content)) {
                    $errors['content'] = 'Заполните поле text';
                }

                if (empty($errors)) {
                    $newId = createPost([
                        'category_id' => $category_id,
                        'title' => $title,
                        'content' => $content,
                        'date' => date('Y-m-d H:i'),
                        'author' => 'Администратор',
                        'image' => $safeFileName,
                    ]);

                    header("Location: /?page=post&id=$newId&success=ok");
                    die();
                }
            }

            echo render('posts/create', [
                'categories' => $categories,
                'title' => $title,
                'category_id' => $category_id,
                'content' => $content,
                'errors' => $errors,
            ]);
            break;

        case $page === 'post-edit':
            if (!isAdmin()) {
                $idForRedirect = isset($_GET['id']) ? (int)$_GET['id'] : '';
                $redirect = '/?page=post-edit&action=edit&id=' . $idForRedirect;
                header('Location: /?page=login&redirect=' . urlencode($redirect));
                exit;
            }
            $category_id = null;
            $post = [];
            $id = null;
            $title = '';
            $content = '';
            $errors = [];
            $action = $_GET['action'] ?? '';

            switch ($action) {
                case 'edit':
                    $id = (int)$_GET['id'];
                    $post = getPost($id);
                    break;

                case 'save':
                    $id = (int)($_POST['id'] ?? null);
                    $title = htmlspecialchars($_POST['title'] ?? '');
                    $content = htmlspecialchars($_POST['content'] ?? '');
                    $category_id = (int)($_POST['category_id'] ?? null);

                    if (empty($title)) {
                        $errors['title'] = 'Заполните поле title';
                    }

                    if (empty($content)) {
                        $errors['content'] = 'Заполните поле text';
                    }

                    $validated = [
                        'id' => $id,
                        'category_id' => $category_id,
                        'title' => $title,
                        'content' => $content,
                    ];

                    if (empty($errors)) {
                        updatePost($validated);

                        header("Location: /?page=post&id=$id&success=edit");
                        die();
                    }
                    break;
                default:
                    throw new OutOfBoundsException('Не верный action');
            }

            $categories = getCategories();

            echo render('posts/edit', [
                'post' => $post,
                'categories' => $categories,
                'title' => $title,
                'category_id' => $category_id,
                'content' => $content,
                'errors' => $errors,
                'id' => $id,
            ]);
            break;

        case $page === 'categories':
            $categories = getCategories();

            echo render('categories', [
                'categories' => $categories,
            ]);
            break;

        case $page === 'posts-category':
            $slug = $_GET['category'] ?? null;

            if (is_null($slug)) {
                throw new OutOfBoundsException('Slug категории не передан');
            }

            $category = getCategoryBySlug($slug);
            $posts = enrichPostsWithLikes(getPostsCategoriesBySlug($slug));

            echo render('posts/posts-category', [
                'posts' => $posts,
                'category' => $category,
                'isAdmin' => isAdmin(),
            ]);
            break;

        case $page === 'error-handler':
            $errorConfig = [
                404 => [
                    'title' => 'Страница не найдена',
                    'message' => 'Запрашиваемая страница не существует или была перемещена.',
                    'suggestions' => [
                        'Проверьте правильность URL адреса',
                        'Вернитесь на главную страницу',
                        'Воспользуйтесь поиском по сайту',
                    ],
                ],
                500 => [
                    'title' => 'Внутренняя ошибка сервера',
                    'message' => 'На сервере произошла техническая ошибка.',
                    'suggestions' => [
                        'Попробуйте обновить страницу через несколько минут',
                        'Очистите кэш браузера',
                        'Сообщите об ошибке администратору',
                        'Попробуйте зайти позже',
                    ],
                ],
            ];

            $errorCode = isset($_GET['code']) ? (int)$_GET['code'] : 404;
            $errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : null;
            $errorId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

            if (!array_key_exists($errorCode, $errorConfig)) {
                $errorCode = 404;
            }

            $config = $errorConfig[$errorCode] ?? $errorConfig[404];
            if ($errorMessage) {
                $config['message'] = htmlspecialchars($errorMessage);
            }

            http_response_code($errorCode);
            header('X-Robots-Tag: noindex, nofollow');

            echo render_template('error', [
                'errorCode' => $errorCode,
                'config' => $config,
                'errorId' => $errorId,
            ]);
            break;

        default:
            redirectToError('404');
    }
} catch (OutOfBoundsException $e) {
    $errorId = 'ERR_' . date('Ymd_His') . '_' . uniqid();
    $errorDetails = [
        'message' => $e->getMessage(),
        'errorId' => $errorId,
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    redirectToError(404, $e->getMessage(), $errorId);
} catch (Exception $e) {
    $errorDetails = [
        'message' => $e->getMessage(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];
    error_log(json_encode($errorDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    redirectToError(500);
}
