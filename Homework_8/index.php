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

session_start();


$theme = $_COOKIE['theme'] ?? 'light';
if (!in_array($theme, ['light', 'dark'], true)) {
    $theme = 'light';
}
$_COOKIE['theme'] = $theme;


$visitCount = isset($_COOKIE['visit_count']) ? (int)$_COOKIE['visit_count'] + 1 : 1;
setcookie('visit_count', (string)$visitCount, time() + 3600 * 24 * 30, '/');
$_COOKIE['visit_count'] = (string)$visitCount; 


if (!isset($_SESSION['page_views'])) {
    $_SESSION['page_views'] = 0;
}
$_SESSION['page_views']++;



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

        case $page === 'clear-cookies':
            // Clear demo cookies (visit counter + theme). Other cookies untouched.
            setcookie('visit_count', '', time() - 3600, '/');
            setcookie('theme', '', time() - 3600, '/');
            unset($_COOKIE['visit_count'], $_COOKIE['theme']);
            // Try to return user to the page they were on
            $redirect = $_GET['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? '/');
            header('Location: ' . $redirect);
            exit;

        case $page === 'clear-session':
            // Reset our session demo data and regenerate id (likes will see user as new)
            $_SESSION = [];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
            session_start();
            session_regenerate_id(true);
            $redirect = $_GET['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? '/');
            header('Location: ' . $redirect);
            exit;

        case $page === 'index':
            echo render('index');
            break;

        case $page === 'posts':
            if (isset($_GET['action']) && $_GET['action'] === 'delete') {
                $id = $_GET['id'] ?? null;
                $posts = getPosts();
                unset($posts[$id]);
                file_put_contents(__DIR__ . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

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
                    $posts = getPosts();

                    $posts[] = [
                        'category_id' => $category_id,
                        'title' => $title,
                        'content' => $content,
                        'date' => date('Y-m-d H:i'),
                        'author' => 'Guest',
                    ];

                    $lastKey = array_key_last($posts);
                    $posts[$lastKey]['id'] = $lastKey;
                    $posts[$lastKey] = array_merge(['id' => $lastKey], $posts[$lastKey]);
                    $posts[$lastKey]['image'] = $safeFileName;

                    file_put_contents(__DIR__ . '/data/posts.json', json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

                    header("Location: /?page=post&id=$lastKey&success=ok");
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
