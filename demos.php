<?php
$data = 65536;
for ($i=1; $i<=6; $i++) {
    $data *= 65536;
}

$data = $data*hexdec('ffff');

$data = explode(',',number_format($data));

$demo = true;
$number = 0;
while ($demo) {
    if (isset($data[$number])) {
        $number_list = $number_list.$data[$number];
        $number ++;
    } else {
        $demo = false;
    }
}

echo $number_list;