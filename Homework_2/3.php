<?php

// https://github.com/AlexGlazing/php-homework-Web511.git
$n = 10;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table>
        <?php for ($i = 1; $i <= $n; ++$i) {
            if ($i & 1) { ?> 
        <tr><td style="border: 1px solid; padding-right: 20px;"><?php echo $i; ?></td></tr>
        <?php } else { ?>
        <tr><td style="border: 1px solid; padding-right: 20px; background-color: black; color: white;"><?php echo $i; ?></td></tr>
            <?php } ?>
        <?php } ?>
    </table>
</body>
</html>