<?php

class ApiFunction
{
    // 基本函数
    public function ip() {
        // 日志记录时间
        return $_SERVER["REMOTE_ADDR"];
    }
    public function now_time() {
        return date('Y-m-d H:i:s');
    }

    // 写入Logs函数
    /**
     * @param $data
     * @param $type
     * @param $function
     * @param string $parameter
     * @return void
     */
    public function logs($data, $type, $function, string $parameter=''): void
    {
        global $SqlConn, $setting;
        if ($function == 0) {
            $function = '失败';
        } else {
            $function = '成功';
        }
        if (empty($parameter)) {
            mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('$data','（" . $type . "）调用" . $function . "',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
        } else {
            if ($function == '失败') {
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('$data','（" . $type . "）调用" . $function . "，参数缺失/错误（".$parameter."）',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
            } else {
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('$data','（" . $type . "）开发错误，请查询原因（错误位置：function_logs）',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
            }
        }
    }
    // 获取密钥
    /**
     * @return mixed
     */
    public function Get_SSID() {
        global $SqlConn,$setting;
        // 密钥获取
        $result_ssid = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['info']." WHERE info='session'");
        $SsidObject = mysqli_fetch_object($result_ssid);
        return $SsidObject->text;
    }

    // 获取用户 ukey （普通密钥）
    /**
     * @param $ukey
     * @return bool
     */
    public function Get_ukey($ukey): bool {
        // 判断key
        if (empty($ukey)) {
            return false;
        } else {
            global $SqlConn,$setting;
            // 获取用户数据库
            $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='$ukey'");
            $Result_User_Object = mysqli_fetch_object($Result_User);
            // 判断用户是否有权限
            if ($Result_User_Object->id != null and $Result_User_Object->ban != 1) {
                // 写入Log
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_ukey','（密钥查询）调用成功，调用ukey=$ukey',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
                return true;
            } else {
                // 写入Log
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_ukey','（密钥查询）调用失败，调用ukey=$ukey',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
                return false;
            }
        }
    }

    // 获取用户ssid（高级密钥）
    /**
     * @param $ssid
     * @return bool
     */
    public function Get_UserSSID($ssid): bool {
        // 判断SSID
        if (empty($ssid)) {
            return false;
        } else {
            global $SqlConn,$setting;
            // 获取用户数据库
            $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user_ssid']." a LEFT JOIN ".$setting['TABLE']['user']." b ON a.user_id=b.id WHERE ssid='$ssid'");
            $Result_User_Object = mysqli_fetch_object($Result_User);
            if ($Result_User_Object->id != null and $Result_User_Object->ban != 1) {
                // 写入Log
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_userSSID','（用户SSID查询）调用成功，调用ssid=$ssid',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
                return true;
            } else {
                // 写入Log
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_userSSID','（用户SSID查询）调用失败，调用ssid=$ssid',NULL,'" . $this->ip() . "','" . $this->now_time() . "')");
                return false;
            }
        }
    }

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

    // 生成新CODE
    // type分三种
        // number -> 纯数字验证码
        // normal -> 数字与大写英文验证码
        // english -> 英文验证码
    /**
     * @param $long
     * @param string $type
     * @return string
     */
    public function Captcha($long, string $type='number'): string {
        for ($i = 1; $i <= $long; $i++) {
            if ($type == 'number') {
                $output = $output.rand(0,9);
            } elseif ($type == 'english') {
                $output = $output.chr(rand(65,90));
            } elseif ($type == 'normal') {
                if (time()%2 == 0) {
                    if ($i % 2 == 0) {
                        $output = $output.chr(rand(65,90));
                    } else {
                        $output = $output.rand(0,9);
                    }
                } else {
                    if ($i % 3 != 0) {
                        $output = $output.chr(rand(65,90));
                    } else {
                        $output = $output.rand(0,9);
                    }
                }
            }
        }
        return $output;
    }
}