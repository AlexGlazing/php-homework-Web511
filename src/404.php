<?php
http_response_code(404);
// 404 page
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../components/menu.php'; ?>
<h2>404 страница не найдена</h2>
</body>
</html>