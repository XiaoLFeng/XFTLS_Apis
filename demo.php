<?php
$max = 200;

$_1 = 0;
$_2 = 0;
$_3 = 0;

for ($num_1 = 1; $num_1 <= $max; $num_1++){
    $_1 = 5 * $num_1 + rand(0,9);
    for ($num_2 = 1; $num_2 <= $max; $num_2++) {
        $_2 = 7 * $num_2 + rand(0,9);
        if ($_1 == $_2) {
            for ($num_3 = 1; $num_3 <= $max; $num_3++) {
                $_3 = 8 * $num_3 + rand(0,9);
                if ($_2 == $_3) {
                    for ($num_4 = 1; $num_4 <= $max; $num_4++) {
                        $_4 = 9 * $num_4 + rand(0,9);
                        if ($_3 == $_4) {
                            echo $_4 . '<br/>';
                        }
                    }
                }
            }
        }
    }
}