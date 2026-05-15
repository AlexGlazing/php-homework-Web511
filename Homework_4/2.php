<?php 

$mas = [4, 5, 1, 4, 7, 8, 15, 6, 71, 45, 2];


$aggregationData = fn(array $array) : array => $newArray = [max($array), min($array), (int)(array_sum($array) / count($array))];

print_r($aggregationData($mas));