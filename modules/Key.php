<?php

class Key
{
    // 生成新ukey
    /**
     * @param $long_salt
     * @return string
     */
    public function ukey_create($long_salt): string {
        $salts = null;
        for ($number = 1;$number <= $long_salt; $number++) {
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
        for ($number = 1;$number <= $long_salt; $number++) {
            $salts = $salts.$salt[$number];
        }
        // 定义 ukey
        return "XFUKEY".rand(1000,9999).time().$salts;
    }

    // 生成新user_ssid
    /**
     * @param $long_salt
     * @return string
     */
    public function ssid_create($long_salt): string {
        $salts = null;
        for ($number = 1;$number <= $long_salt; $number++) {
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
        for ($number = 1;$number <= $long_salt; $number++) {
            $salts = $salts.$salt[$number];
        }
        // 定义 ukey
        return "XFSSID".rand(1000,9999).time().$salts;
    }

    public function service_acgurl_access_key($long_salt) {
        $salts = null;
        for ($number = 1;$number <= $long_salt; $number++) {
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
        for ($number = 1;$number <= $long_salt; $number++) {
            $salts = $salts.$salt[$number];
        }
        // 定义 ukey
        return "XFACG".time().$salts;
    }

    public function okey_create($long_salt): string {
        $salts = null;
        for ($number = 1;$number <= $long_salt; $number++) {
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
        for ($number = 1;$number <= $long_salt; $number++) {
            $salts = $salts.$salt[$number];
        }
        // 定义 ukey
        return "XFOKEY".rand(1000,9999).time().$salts;
    }
}