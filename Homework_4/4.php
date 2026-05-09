<?php

$mas = [4, 5, 1, 4, 7, 8, 15, 6, 71, 45, 2];

function sumMaxMin(array $array) : int {
    $min = $array[0];
    $max = $array[0];
    foreach ($array as $value) {
        if($value < $min){
            $min = $value;
        }
        if($value > $max){
            $max = $value;
        }
    }
    return $result = $max + $min;
}

echo sumMaxMin($mas);