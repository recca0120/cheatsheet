<?php

$parameter = '';
if ($typeHint !== null) {
    if (is_string($typeHint)) {
        $parameter .= $typeHint.' ';
    } else {
        $parameter .= $typeHint['name'].' ';
    }
}

if ($reference === true) {
    $parameter .= '&';
}

$parameter .= '$'.$name;

if ($default !== null) {
    $parameter .= ' = '.$default;
} elseif ($optional) {
    $parameter .= ' = null';
}
echo $parameter;
?>
