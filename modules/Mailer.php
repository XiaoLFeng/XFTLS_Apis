<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendMail
{
    /**
     * 发送邮件函数
     * @param string $type 填写发件模板
     * @param string $email 填写收件人
     * @param string $code [可选]填写需要发送的验证码
     * @param string $time [可选]填写创建时间
     * @param string $usetime [可选]有效期时间
     * @return bool
     */
    public function Mailer(string $type, string $email, string $code='', string $time='', string $usetime=''): bool {
        global $SendMail;
        global $setting;

        require $_SERVER["DOCUMENT_ROOT"].'/plugins/PHPMailer/Exception.php';
        require $_SERVER["DOCUMENT_ROOT"].'/plugins/PHPMailer/PHPMailer.php';
        require $_SERVER["DOCUMENT_ROOT"].'/plugins/PHPMailer/SMTP.php';

        require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Mail_Templates.php';
        $SendMail = new Mail_Templates();

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

    /**
     * 检查通信协议是 HTTP 还是 HTTPS
     * @param string $type [port]获取端口值，[secure]连接模式
     * @return mixed|string|null
     */
    private function SSL_Check(string $type) {
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