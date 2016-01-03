<?php

$temp = [];
foreach ($parameters as $parameter) {
    $temp[] = $parameter->render();
}

echo $modifiers.' function '.$name.'('.implode(', ', $temp).')';
