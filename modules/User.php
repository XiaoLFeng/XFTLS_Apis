<?php

class User
{
    // 用户登录检查单（验证数据的有效性）
    public function login_input_data($email='',$username='',$password): string {
        if (!empty($email) and empty($username)) {
            if (preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)) {
                if (preg_match("/^[\!\@\#\$\%\^\&\*\(\)\,\.\/\<\>\?\;\:\[\]\\|\-\=\ \w+]{6,30}$/", $password)) {
                    return 'TRUE';
                } else {
                    return 'PASSWORD_FALSE';
                }
            } else {
                return 'EMAIL_FALSE';
            }
        } elseif (empty($email) and !empty($username)) {
            if (preg_match('/^\w{3,20}$/', $username)) {
                if (preg_match("/^[\!\@\#\$\%\^\&\*\(\)\,\.\/\<\>\?\;\:\[\]\\|\-\=\ \w+]{6,30}$/", $password)) {
                    return 'TRUE';
                } else {
                    return 'PASSWORD_FALSE';
                }
            } else {
                return 'USERNAME_FALSE';
            }
        } else {
            return 'ERROR';
        }
    }

    // 用户注册检查单（验证数据的有效性）
    public function register_input_data($email,$username,$password,$desc,$regip): string {
        if (!empty($email) and preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)) {
            if (!empty($username) and preg_match('/^\w{3,20}$/', $username)) {
                if (!empty($password) and preg_match("/^[\!\@\#\$\%\^\&\*\(\)\,\.\/\<\>\?\;\:\[\]\\|\-\=\ \w+]{6,30}$/", $password)) {
                    if (preg_match("/[^;' ]+/",$desc)) {
                        if (!empty($regip) and preg_match("/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/",$regip)) {
                            return 'TRUE';
                        } else {
                            return 'REGIP_FALSE';
                        }
                    } else {
                        return 'DESC_FALSE';
                    }
                } else {
                    return 'PASSWORD_FALSE';
                }
            } else {
                return 'USERNAME_FALSE';
            }
        } else {
            return 'EMAIL_FALSE';
        }
    }

    // 用户忘记密码检查单（验证数据的有效性）
    public function forgotpassword_input_data($email,$username,$phone,$code,$password): string {
        if (empty($email) or preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)) {
            if (empty($username) or preg_match('/^\w{3,20}$/', $username)) {
                if (empty($phone) or preg_match("/^(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8}$/", $phone)) {
                    if (empty($password) or preg_match("/^[\!\@\#\$\%\^\&\*\(\)\,\.\/\<\>\?\;\:\[\]\\|\-\=\ \w+]{6,30}$/", $password)) {
                        if (empty($code) or preg_match("/^[A-Z0-9]+$/",$code)) {
                            return 'TRUE';
                        } else {
                            return 'CODE_FALSE';
                        }
                    } else {
                        return 'PASSWORD_FALSE';
                    }
                } else {
                    return 'PHONE_FALSE';
                }
            } else {
                return 'USERNAME_FALSE';
            }
        } else {
            return 'EMAIL_FALSE';
        }
    }

    // 注销用户检查单（验证数据的有效性）
    public function unregister($email,$password,$code): string {
        if (!empty($email) and preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)) {
            if (!empty($password) and preg_match("/^[\!\@\#\$\%\^\&\*\(\)\,\.\/\<\>\?\;\:\[\]\\|\-\=\ \w+]{6,30}$/", $password)) {
                if (empty($code) or preg_match("/^[A-Z0-9]+$/",$code)) {
                    return 'TRUE';
                } else {
                    return 'CODE_FALSE';
                }
            } else {
                return 'PASSWORD_FALSE';
            }
        } else {
            return 'EMAIL_FALSE';
        }
    }
}