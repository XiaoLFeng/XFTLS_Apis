<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER["DOCUMENT_ROOT"].'/plugins/PHPMailer/Exception.php';
require $_SERVER["DOCUMENT_ROOT"].'/plugins/PHPMailer/PHPMailer.php';
require $_SERVER["DOCUMENT_ROOT"].'/plugins/PHPMailer/SMTP.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Mail_Templates.php';

$SendMail = new Mail_Templates();

class SendMail
{
    // 发送邮件函数
    public function Mailer($type,$email,$code='',$time='',$usetime=''): bool {
        global $SendMail;
        global $setting;
        $Mail = new PHPMailer(true);
        try {
            // 服务器配置
            $Mail->CharSet = "UTF-8";
            $Mail->SMTPDebug = 0;
            $Mail->isSMTP();
            $Mail->Host = $setting['SMTP']['HOST'];
            $Mail->SMTPAuth = true;
            $Mail->Username = $setting['SMTP']['USER'];
            $Mail->Password = $setting['SMTP']['PASSWORD'];
            $Mail->SMTPSecure = $this->SSL_Check('secure');
            $Mail->Port = $this->SSL_Check('port');
            $Mail->setFrom('noreplay@x-lf.cn', '筱锋机器人');
            $Mail->addAddress($email);

            $Mail->isHTML(true);
            $Mail->Subject = '筱锋工具箱 - '.$type; // 邮箱标题
            $Mail->Body = $SendMail->Templates($type,$code,$email,$time,$usetime); // 邮箱正文
            $Mail->AltBody = '筱锋工具箱 - '.$type.'：'.$code; // 不支持HTML显示内容

            $Mail->send();
            return true;
        } catch (Exception $e) {
            echo '邮件发送失败：', $Mail->ErrorInfo;
            return false;
        }
    }
    // 检查是否是SSL
    private function SSL_Check($type) {
        global $setting;
        if ($type == 'port') {
            if ($_SERVER['SERVER_PORT'] != '443') {
                return $setting['SMTP']['NOSSL'];
            } else {
                return $setting['SMTP']['SSL'];
            }
        } elseif ($type == 'secure') {
            if ($_SERVER['SERVER_PORT'] != '443') {
                return 'TLS';
            } else {
                return 'ssl';
            }
        } else {
            return null;
        }
    }

}