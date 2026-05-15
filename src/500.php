<?php
http_response_code(500);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ошибка 500</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../components/menu.php'; ?>
<h2>Внутренняя ошибка сервера</h2>
<p>Произошла ошибка сервера. Пожалуйста, попробуйте позже.</p>
</body>
</html>