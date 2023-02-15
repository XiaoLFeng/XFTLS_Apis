<?php

class Key
{
    /**
     * 生成新 ukey
     * @param $long_salt
     * @return string
     */
    public function ukey_create($long_salt): string {
        return "XFUKEY".rand(1000,9999).time(). $this->get_salt($long_salt);
    }

    /**
     * 生成新 user_ssid
     * @param $long_salt
     * @return string
     */
    public function ssid_create($long_salt): string {
        // 定义 ukey
        return "XFSSID".rand(1000,9999).time(). $this->get_salt($long_salt);
    }

    /**
     * 生成新 随机图库AccessKey
     * @param $long_salt
     * @return string
     */
    public function service_acgurl_access_key($long_salt): string
    {
        return "XFACG".time(). $this->get_salt($long_salt);
    }

    /**
     * 生成新 开源库Key
     * @param $long_salt
     * @return string
     */
    public function okey_create($long_salt): string {
        return "XFOKEY".rand(1000,9999).time(). $this->get_salt($long_salt);
    }

    /**
     * 生成新 网站分析sid
     * @param $long_salt
     * @return string
     */
    public function service_analytics_create($long_salt): string {
        return "AYSID".time(). $this->get_salt($long_salt);
    }

    /**
     * 生成新 网站分析sid
     * @param $long_salt
     * @return string
     */
    public function service_analytics_user_create($long_salt): string {
        return "AYUSER".time(). $this->get_salt($long_salt);
    }

    /**
     * 生成一组随机字符
     * @param int $length 输入需要输出字符串的长度
     * @return string 返回字符内容包含 A-Z,a-z,0-9 的一串内容
     */
    public function Rand_String(int $length): string {
        $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $strlen = strlen($string)-1;
        $rand_string = null;
        for ($i=0; $i<$length; $i++) {
            $number = mt_rand(0,$strlen);
            $rand_string .= $string[$number];
        }
        return $rand_string;
    }

    /**
     * 创建密钥模板
     * @param $long_salt
     * @return string|null
     */
    private function get_salt($long_salt): ?string
    {
        $salt = null;
        $salts = null;
        for ($number = 1; $number <= $long_salt; $number++) {
            if (time() % 2 == 0) {
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
        for ($number = 1; $number <= $long_salt; $number++) {
            $salts = $salts . $salt[$number];
        }
        return $salts;
    }
}