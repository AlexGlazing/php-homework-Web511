<?php

// https://github.com/AlexGlazing/php-homework-Web511.git
do {
    // code...
    $i = readline('Подскажи свой возраст! Диапазон от 0 до 100: ');
    if ($i <= 0 || $i >= 100) {
        echo "\033[31mError!!!\033[0m\n";
    }
} while ($i <= 0 || $i >= 100);
