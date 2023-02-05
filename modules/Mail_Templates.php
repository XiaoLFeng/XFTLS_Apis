<?php

class Mail_Templates
{
    // 检查使用邮件发送模板
    public function Templates($type,$G_code,$G_email,$G_endTime,$G_usetime) {
        if ($type == 'code') {
            return $this->Captcha($G_code,$G_email,$G_endTime,$G_usetime);
        } elseif ($type == 'register') {
            return $this->Captcha($G_code,$G_email,$G_endTime,$G_usetime);
        } elseif ($type == 'register_over') {
            return $this->register($G_email);
        } elseif ($type == 'forgotpassword') {
            return $this->Captcha($G_code,$G_email,$G_endTime,$G_usetime);
        } elseif ($type == 'forgot_check') {
            return $this->forgot_password($G_email);
        } elseif ($type == 'unregister') {
            return $this->Captcha($G_code,$G_email,$G_endTime,$G_usetime);
        } elseif ($type == 'unregister_over') {
            return $this->unregister($G_email);
        }
    }
    // 发送验证码
    public function Captcha($G_code, $G_email, $G_endTime, $G_usetime): string {
        $G_endTimes = date("Y-m-d H:i:s",$G_endTime+$G_usetime);
        $G_usetime = $G_usetime/60;;
        $G_year = date("Y");
        $G_date = date("Y-m-d H:i:s");
        return <<<EOF
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>XF_TLS_MAIL</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            </head>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;border: 1px solid #cccccc;box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175)">
                <tr>
                    <td align="center" bgcolor="#70bbd9" style="padding: 30px 0 30px 0;">
                        <img src="https://api.x-lf.cn/sources/img/mail_logo.png" alt="EmailLogo" width="300" height="65" style="display: block;" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 30px 30px 30px 30px;">
                            <tr>
                                <td style="padding: 10px 0px 30px 0px;color: #08212b; font-family: Arial, sans-serif; font-size: 10px;">
                                    时间： <font style="font-family: var(--bs-font-monospace)">$G_date</font>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0px 0px 10px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 24px;">
                                    Dear. <a style="text-decoration: none;color: #198754;" href="mailto:$G_email">$G_email</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0px 5px 5px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 20px;">
                                    您的验证码为：<strong>$G_code</strong><br/>
                                    您的验证码 <strong>$G_usetime</strong> 分钟内有效<br/>
                                    有效期至：$G_endTimes
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#f0f0f0" style="padding: 30px 20px 30px 20px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="font-family: Arial, sans-serif; font-size: 14px;">
                                    <font style="color: grey;">&copy; 2020 - $G_year <a style="text-decoration: none;color: #198754;" href="https://www.x-lf.com">筱锋xiao_lfeng</a> All Rights Reserved.</font><br/>
                                    <font style="color: grey;">本邮件为 <a href="https://www.x-lf.com" style="text-decoration: none;color: #198754;">XF_Mail</a> 自动发出，请勿直接回复</font>
                                </td>
                                <td>
                                    <a href="mailto:gm@x-lf.cn">
                                        <img src="https://api.x-lf.cn/sources/img/img.png" alt="Email" width="38" height="38" style="display: block;" border="0" />
                                    </a>
                                </td>
                                <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <tr>
                <td style="padding: 30px 0 20px 0;"></td>
            </tr>
            </html>
            EOF;
    }
    // 注册用户
    public function register($G_email): string {
        $G_ip = $_SERVER['REMOTE_ADDR'];
        $G_date = date("Y-m-d H:i:s");
        $G_year = date("Y");
        return <<<EOF
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>XF_TLS_MAIL</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;border: 1px solid #cccccc;box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175)">
            <tr>
                <td align="center" bgcolor="#70bbd9" style="padding: 30px 0 30px 0;">
                    <img src="https://api.x-lf.cn/sources/img/mail_logo.png" alt="EmailLogo" width="300" height="65" style="display: block;" />
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 30px 30px 30px 30px;">
                        <tr>
                            <td style="padding: 10px 0px 30px 0px;color: #08212b; font-family: Arial, sans-serif; font-size: 10px;">
                                时间： <font style="font-family: var(--bs-font-monospace)">$G_date</font>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 0px 10px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 24px;">
                                Dear. $G_email
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 5px 5px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 15px;">
                                感谢您注册 <a href="https://www.x-lf.cn/" style="text-decoration: none;color: #198754;">筱锋工具箱（XF_TLS）</a> ，在这里你可以体验简便的工具操作，以及通用API调用。<br/>
                                更多操作请在工具箱控制台中进行查看~
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 5px 5px 0px;color: #6c6c6c; font-family: Arial, sans-serif; font-size: 15px;">
                                如果您未在本站注册，则您的邮箱或手机号验证码发生了泄露。建议您修改密码或检查设施。如果您对本站无需求，执行修改密码后进行用户注销。
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f0f0f0" style="padding: 30px 20px 30px 20px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="font-family: Arial, sans-serif; font-size: 14px;">
                                <font style="color: grey;">&copy; 2020 - $G_year <a style="text-decoration: none;color: #198754;" href="https://www.x-lf.com">筱锋xiao_lfeng</a> All Rights Reserved.</font><br/>
                                <font style="color: grey;">本邮件为 <a href="https://www.x-lf.com" style="text-decoration: none;color: #198754;">XF_Mail</a> 自动发出，请勿直接回复</font>
                            </td>
                            <td>
                                <a href="mailto:gm@x-lf.cn">
                                    <img src="https://api.x-lf.cn/sources/img/img.png" alt="Email" width="38" height="38" style="display: block;" border="0" />
                                </a>
                            </td>
                            <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <tr>
            <td style="padding: 30px 0 20px 0;"></td>
        </tr>
        </html>
        EOF;

    }
    // 重置密码通知
    public function forgot_password($G_email): string {
        $G_ip = $_SERVER['REMOTE_ADDR'];
        $G_date = date("Y-m-d H:i:s");
        $G_year = date("Y");
        return <<<EOF
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>XF_TLS_MAIL</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;border: 1px solid #cccccc;box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175)">
            <tr>
                <td align="center" bgcolor="#70bbd9" style="padding: 30px 0 30px 0;">
                    <img src="https://api.x-lf.cn/sources/img/mail_logo.png" alt="EmailLogo" width="300" height="65" style="display: block;" />
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 30px 30px 30px 30px;">
                        <tr>
                            <td style="padding: 10px 0px 30px 0px;color: #08212b; font-family: Arial, sans-serif; font-size: 10px;">
                                时间： <font style="font-family: var(--bs-font-monospace)">$G_date</font>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 0px 10px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 24px;">
                                Dear. $G_email
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 5px 5px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 15px;">
                                您在 <a href="https://www.x-lf.cn/" style="text-decoration: none;color: #198754;">筱锋工具箱（XF_TLS）</a>站点中，已成功需改密码！
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 5px 5px 0px;color: #6c6c6c; font-family: Arial, sans-serif; font-size: 15px;">
                                若您未在本站操作，则您的邮箱或手机号验证码发生了泄露。建议您修改密码或检查设施。
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f0f0f0" style="padding: 30px 20px 30px 20px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="font-family: Arial, sans-serif; font-size: 14px;">
                                <font style="color: grey;">&copy; 2020 - $G_year <a style="text-decoration: none;color: #198754;" href="https://www.x-lf.com">筱锋xiao_lfeng</a> All Rights Reserved.</font><br/>
                                <font style="color: grey;">本邮件为 <a href="https://www.x-lf.com" style="text-decoration: none;color: #198754;">XF_Mail</a> 自动发出，请勿直接回复</font>
                            </td>
                            <td>
                                <a href="mailto:gm@x-lf.cn">
                                    <img src="https://api.x-lf.cn/sources/img/img.png" alt="Email" width="38" height="38" style="display: block;" border="0" />
                                </a>
                            </td>
                            <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <tr>
            <td style="padding: 30px 0 20px 0;"></td>
        </tr>
        </html>
        EOF;
    }

    // 用户注销通知
    public function unregister($G_email): string {
        $G_ip = $_SERVER['REMOTE_ADDR'];
        $G_date = date("Y-m-d H:i:s");
        $G_year = date("Y");
        return <<<EOF
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>XF_TLS_MAIL</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;border: 1px solid #cccccc;box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175)">
            <tr>
                <td align="center" bgcolor="#70bbd9" style="padding: 30px 0 30px 0;">
                    <img src="https://api.x-lf.cn/sources/img/mail_logo.png" alt="EmailLogo" width="300" height="65" style="display: block;" />
                </td>
            </tr>
            <tr>
                <td>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding: 30px 30px 30px 30px;">
                        <tr>
                            <td style="padding: 10px 0px 30px 0px;color: #08212b; font-family: Arial, sans-serif; font-size: 10px;">
                                时间： <font style="font-family: var(--bs-font-monospace)">$G_date</font>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 0px 10px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 24px;">
                                Dear. $G_email
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 5px 5px 0px;color: #000000; font-family: Arial, sans-serif; font-size: 15px;">
                                您在 <a href="https://www.x-lf.cn/" style="text-decoration: none;color: #198754;">筱锋工具箱（XF_TLS）</a>站点中，已成功注销账户！
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0px 5px 5px 0px;color: #6c6c6c; font-family: Arial, sans-serif; font-size: 15px;">
                                若您未在本站操作，则您的邮箱或手机号验证码发生了泄露。请联系管理员重新注册！
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td bgcolor="#f0f0f0" style="padding: 30px 20px 30px 20px;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td style="font-family: Arial, sans-serif; font-size: 14px;">
                                <font style="color: grey;">&copy; 2020 - $G_year <a style="text-decoration: none;color: #198754;" href="https://www.x-lf.com">筱锋xiao_lfeng</a> All Rights Reserved.</font><br/>
                                <font style="color: grey;">本邮件为 <a href="https://www.x-lf.com" style="text-decoration: none;color: #198754;">XF_Mail</a> 自动发出，请勿直接回复</font>
                            </td>
                            <td>
                                <a href="mailto:gm@x-lf.cn">
                                    <img src="https://api.x-lf.cn/sources/img/img.png" alt="Email" width="38" height="38" style="display: block;" border="0" />
                                </a>
                            </td>
                            <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <tr>
            <td style="padding: 30px 0 20px 0;"></td>
        </tr>
        </html>
        EOF;
    }
}