<?php

namespace CompanyName\Blog;

function uploadImage(array &$errors, string $fieldName = 'image'): ?string
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        $errors[$fieldName] = 'Ошибка загрузки файла';

        return null;
    }

    $extensionMimeMap = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];
    $maxFileSize = 5 * 1024 * 1024;

    if ($_FILES[$fieldName]['size'] > $maxFileSize) {
        $errors[$fieldName] = 'Файл слишком большой';

        return null;
    }

    $fileName = $_FILES[$fieldName]['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!array_key_exists($fileExtension, $extensionMimeMap)) {
        $errors[$fieldName] = 'Не правильный тип файла';

        return null;
    }

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMimeType = finfo_file($finfo, $_FILES[$fieldName]['tmp_name']);
        finfo_close($finfo);

        if ($detectedMimeType !== false && $extensionMimeMap[$fileExtension] !== $detectedMimeType) {
            $errors[$fieldName] = 'Не правильный тип файла';

            return null;
        }
    }

    $safeFileName = uniqid() . '_' . date('Y-m-d_H-i-s') . '.' . $fileExtension;

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0777, true);
    }

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], UPLOAD_PATH . '/' . $safeFileName)) {
        $errors[$fieldName] = 'Файл не загружен';

        return null;
    }

    return $safeFileName;
}

function validatePostInput(array &$errors): array
{
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($title === '') {
        $errors['title'] = 'Заполните поле title';
    }

    if ($content === '') {
        $errors['content'] = 'Заполните поле text';
    }

    return [
        'title' => htmlspecialchars($title),
        'content' => htmlspecialchars($content),
        'category_id' => $categoryId > 0 ? $categoryId : null,
    ];
}
