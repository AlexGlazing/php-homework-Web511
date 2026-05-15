<?php
// Header template - includes DOCTYPE, head, opening body and navigation menu
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Блог' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../components/menu.php'; ?>