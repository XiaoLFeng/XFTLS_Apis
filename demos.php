<?php
$salts = null;
for ($number = 1;$number <= 10; $number++) {
    if (time()%2 == 0) {
        if ($number % 2 == 0 or $number % 3 == 0) {
            $salt[$number] = chr(rand(97, 122));
        } else {
            $salt[$number] = rand(0, 9);
        }
    } else {
        if ($number % 2 != 0 or $number % 3 != 0) {
            $salt[$number] = chr(rand(97, 122));
        } else {
            $salt[$number] = rand(0, 9);
        }
    }
}
for ($number = 1;$number <= 10; $number++) {
    $salts = $salts.$salt[$number];
}
// 定义 ukey
echo "XFUKEY".rand(1000,9999).time().$salts;