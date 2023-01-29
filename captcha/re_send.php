<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var mysqli $SqlConn 数据库链接参数
 * @var string $setting 配置文件
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/User.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Mailer.php';
$ApiFunction = new ApiFunction();
$User = new User();
$Mail = new SendMail();

// 获取参数
// POST
$GetPost = file_get_contents('php://input');
$PostData = json_decode($GetPost,true);

// 函数构建
if ($ApiFunction->Get_UserSSID($PostData['ssid'])) {
    // 检查验证码
    // 检索数据库
    $Result_Captcha = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['captcha']." WHERE `email`='".$PostData['data']['P_email']."' AND `type`='".$PostData['data']['P_type']."' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
    $Result_Captcha_Object = mysqli_fetch_object($Result_Captcha);

    // 检查数据是否存在
    if ($Result_Captcha_Object->id != null) {
        // 检查重发验证码
        if ($Mail->Mailer($Result_Captcha_Object->type,$PostData['data']['P_email'],$Result_Captcha_Object->code,$Result_Captcha_Object->time,$setting['CAPTCHA_TIME'])) {
            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'验证码已发送',
            );
            // 输出数据
            $ApiFunction->logs('mailer_recode','重发验证码',1);
        } else {
            // 编译数据
            $data = array(
                'output'=>'SEND_EMAIL_FAIL',
                'code'=>403,
                'info'=>'参数 Server[Send_Email_Fail] 缺失/错误，重发验证码失败',
            );
            // 输出数据
            $ApiFunction->logs('mailer_recode','重发验证码',0,'Server_SendEmailFail');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'CAPTCHA_ERROR',
            'code'=>403,
            'info'=>'没有需要重新发送的验证码',
        );
        // 输出数据
        $ApiFunction->logs('mailer_recode','重发验证码',0,'Server_CaptchaNoResend');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'UEKY_DENY',
        'code'=>403,
        'info'=>'参数 Post[ukey] 缺失/错误',
    );
    // 输出数据
    $ApiFunction->logs('mailer_recode','密钥检查',0,'Post_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);