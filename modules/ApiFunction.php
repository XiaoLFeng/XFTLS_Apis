<?php

class ApiFunction
{
    /**
     * 写入Logs函数
     * @param $data string 参数简称
     * @param $type string 主要内容
     * @param $function int 是否是成功的
     * @param string $parameter 错误原因
     * @return void 不返回值
     */
    public function logs(string $data, string $type, int $function, string $parameter=''): void
    {
        global $SqlConn, $setting;
        if ($function == 0) {
            $function = '失败';
        } else {
            $function = '成功';
        }
        if (empty($parameter)) {
            mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (`belong`,`info`,`remark`,`ip`,`referer`,`time`) VALUES ('$data','（" . $type . "）调用" . $function . "',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','".$_SERVER['HTTP_REFERER']."','" . date('Y-m-d H:i:s') . "')");
        } else {
            if ($function == '失败') {
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (`belong`,`info`,`remark`,`ip`,`referer`,`time`) VALUES ('$data','（" . $type . "）调用" . $function . "，参数缺失/错误（".$parameter."）',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','".$_SERVER['HTTP_REFERER']."','" . date('Y-m-d H:i:s') . "')");
            } else {
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (`belong`,`info`,`remark`,`ip`,`referer`,`time`) VALUES ('$data','（" . $type . "）开发错误，请查询原因（错误位置：function_logs）',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','".$_SERVER['HTTP_REFERER']."','" . date('Y-m-d H:i:s') . "')");
            }
        }
    }
    // 获取密钥

    /**
     * @param $session
     * @return bool
     */
    public function Check_Session($session): bool {
        if (empty($session)) return false;
        else {
            global $SqlConn,$setting;
            // 密钥获取
            $Result_Session = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['info']." WHERE info='session'");
            $Result_Session_Object = mysqli_fetch_object($Result_Session);
            if ($Result_Session_Object->text == $session) return true;
            else return false;
        }

    }

    // 获取用户 ukey （普通密钥）
    /**
     * @param $ukey
     * @return bool 返回值
     */
    public function Check_Ukey($ukey): bool {
        if (preg_match('/^XFUKEY[0-9]{14}[A-za-z0-9]{10}/',$ukey)) {
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
                    mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_ukey','（密钥查询）调用成功，调用ukey=$ukey',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','" . date('Y-m-d H:i:s') . "')");
                    return true;
                } else {
                    // 写入Log
                    mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_ukey','（密钥查询）调用失败，调用ukey=$ukey',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','" . date('Y-m-d H:i:s') . "')");
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function Get_okey(string $okey): bool {
        if (preg_match('/^XFOKEY[0-9]{14}[A-za-z0-9]{10}/',$okey)) {
            // 判断key
            if (empty($okey)) {
                return false;
            } else {
                global $SqlConn,$setting;
                // 获取用户数据库
                $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['okey']." a LEFT JOIN ".$setting['TABLE']['user']." b ON a.user_id=b.id WHERE okey='$okey'");
                $Result_User_Object = mysqli_fetch_object($Result_User);
                // 判断用户是否有权限
                if ($Result_User_Object->id != null and $Result_User_Object->ban != 1) {
                    // 写入Log
                    mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_okey','（密钥查询）调用成功，调用okey=$okey',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','" . date('Y-m-d H:i:s') . "')");
                    return true;
                } else {
                    // 写入Log
                    mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_okey','（密钥查询）调用失败，调用okey=$okey',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','" . date('Y-m-d H:i:s') . "')");
                    return false;
                }
            }
        } else {
            return false;
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
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_userSSID','（用户SSID查询）调用成功，调用ssid=$ssid',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','" . date('Y-m-d H:i:s') . "')");
                return true;
            } else {
                // 写入Log
                mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['logs'] . " (belong,info,remark,ip,time) VALUES ('search_userSSID','（用户SSID查询）调用失败，调用ssid=$ssid',NULL,'" . $_SERVER["REMOTE_ADDR"] . "','" . date('Y-m-d H:i:s') . "')");
                return false;
            }
        }
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