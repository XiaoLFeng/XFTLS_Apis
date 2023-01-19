<?php

class calc_for_ipv6
{
    public function calc_ipv6(array $ip): string
    {
        $data = pow(65536,7)*hexdec($ip[0]);
        $data = explode(',',number_format($data));
        $number = 0;
        while ($number <= 14) {
            $output = $output.$data[$number];
            $number ++;
        }

        return $output;
    }
}