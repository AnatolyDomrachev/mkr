<?php

$pole_number = trim(fgets(STDIN));
$output = '';

for($i = 1; $i <= $pole_number; $i++) {
    $n_count = trim(fgets(STDIN));

    $pole = array();
    for($k = 1; $k <= $n_count; $k++) {
        $pole[] = explode(' ', trim(fgets(STDIN)));
    }

    $output .= ((isGood($pole)) ? 'YES' : 'NO') . PHP_EOL;
}

echo $output;
exit;

function isGood(array $pole)
{
    if(count($pole) == 1) {
        $el = array_shift(array_shift($pole));
        return $el == 0;
    }

    $lastEl = null;
    foreach($pole as $row) {
        foreach($row as $el) {
            if($el == 0 || ($el == $lastEl)) {
                return true;
            }
            $lastEl = $el;
        }
        $lastEl = null;
    }
    $lastRow = array_shift($pole);
    foreach($pole as $row) {
        foreach($row as $i => $el) {
            if($el == $lastRow[$i]) {
                return true;
            }
        }
        $lastRow = $row;
    }
    return false;
}