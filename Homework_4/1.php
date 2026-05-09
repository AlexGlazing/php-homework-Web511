<?php

$array = [4, 5, 1, 4, 7, 8, 15, 6, 71, 45, 2];

function evenOdd(array $array): array
{
    $newArr = [];
    foreach ($array as $value) {
        if($value & 1){
            array_push($newArr, "не четное");
        }
        else{
            array_push($newArr, "четное");
        }
    }
    return $newArr;
}

print_r(evenOdd($array));

# Сделать через array_map
$altEvenOdd = fn(int $value) => $value & 1 ? "не четное" : "четное";
print_r(array_map($altEvenOdd, $array));