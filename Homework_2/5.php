<?php

// https://github.com/AlexGlazing/php-homework-Web511.git
start:
$res = readline('Сколько будет 2+2, Варианты: 2 3 4. ');

switch ($res) {
    case 2:
    case 3:
        echo 'Не верно!';
        break;
    case 4:
        echo 'Поздравляю, правильный ответ!';
        break;
    default:
        goto start;
}
