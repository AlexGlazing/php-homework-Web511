<?php

$errors = [];

$arg1 = 0;
$arg2 = 0;
$result = 0;
$operation = '';
$textResult = '';

if (!empty($_GET)) {
    $arg1 = $_GET["arg1"] ?? '';
    $arg2 = $_GET["arg2"] ?? '';
    $operation = $_GET["operation"] ?? '';

    //валидация ввод
    if ($arg1 === '' || $arg2 === '') {
        $errors[] = "Поле не должно быть пустым";
    }

    if (!(is_numeric($arg1) && is_numeric($arg2))) {
        $errors[] = "Введите число, а не строку";
    }

    if (!in_array($operation, ["+", "-", "*", "/"])) {
        $errors[] = "Не верная операция";
    }

/*    if (!$errors) {
        header('Location: calculator.php?operation=' . $operation . '&arg1=' . $arg1 . '&arg2=' . $arg2&err);
        die();
    }*/

    if (empty($errors)) {
        $arg1 = (float)$arg1;
        $arg2 = (float)$arg2;

        $result = match ($operation) {
            '+' => $arg1 + $arg2,
            '-' => $arg1 - $arg2,
            '*' => $arg1 * $arg2,
            '/' => ($arg2 !== 0.0) ? $arg1 / $arg2 : "Деление на 0",
            default => throw new Exception("Ошибка"),
        };

        if (is_numeric($result)) {
            $result = round($result, 2);
        }

        $textResult = "$arg1 $operation $arg2 = $result";
    }

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Калькулятор</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body
<?php include __DIR__ . '/components/menu.php' ?>
<h2>Калькулятор</h2>
<form action="" method="get">
    <input type="text" name="arg1" value="<?= htmlspecialchars($arg1) ?>">
    <input style="border: 2px solid <?= $operation === '+' ? 'green' : 'white' ?>" type="submit" name="operation"
           value="+">
    <input style="border: 2px solid <?= $operation === '-' ? 'green' : 'white' ?>" type="submit" name="operation"
           value="-">
    <input style="border: 2px solid <?= $operation === '*' ? 'green' : 'white' ?>" type="submit" name="operation"
           value="*">
    <input style="border: 2px solid <?= $operation === '/' ? 'green' : 'white' ?>" type="submit" name="operation"
           value="/">
    <input type="text" name="arg2" value="<?= htmlspecialchars($arg2) ?>">
    =
    <input type="text" readonly value="<?= htmlspecialchars($result) ?>">
</form>
<?php if (!$errors): ?>
    <p>Результат: <?= htmlspecialchars($textResult) ?></p>
<?php else: ?>
    <?php foreach ($errors as $error): ?>
        <p style="color:red">Ошибка: <?= htmlspecialchars($error) ?></p>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
</body>
</html>
