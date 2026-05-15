<?php

$array = [];

do {
    array_push($array, mt_rand(1, 200));
    $array = array_unique($array);
} while (count($array) <= 100);

print_r($array);

