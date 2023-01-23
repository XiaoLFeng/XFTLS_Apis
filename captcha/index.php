<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/** @var TYPE_NAME $SqlConn */
/** @var TYPE_NAME $setting */

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

// 函数处理
if ($ApiFunction->Get_UserSSID($PostData['ssid'])) {
    // 检查输入数据是否正确
    if (!empty($PostData['data']['P_email']) and empty($PostData['data']['P_phone']) and preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $PostData['data']['P_email'])) {
        // 检索数据库
        $Result_Captcha = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['captcha']." WHERE `email`='".$PostData['data']['P_email']."' AND `type`='".$PostData['data']['P_type']."' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
        $Result_Captcha_Object = mysqli_fetch_object($Result_Captcha);

        // 检查数据是否存在
        if ($Result_Captcha_Object->id == null) {
            // 新建code
            $code = $ApiFunction->Captcha(6);
            if (mysqli_query($SqlConn, "INSERT INTO ".$setting['TABLE']['captcha']." (`email`,`code`,`type`,`time`) VALUES ('".$PostData['data']['P_email']."','".$code."','".$PostData['data']['P_type']."','".time()."')")) {
                // 检查邮件发送
                if ($Mail->Mailer('code',$PostData['data']['P_email'],$code,time(),$setting['CAPTCHA_TIME'])) {
                    // 编译数据
                    $data = array(
                        'output'=>'SUCCESS',
                        'code'=>200,
                        'info'=>'验证码已发送',
                    );
                    // 输出数据
                    $ApiFunction->logs('mailer_code','邮件发送',1);
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'SEND_EMAIL_FAIL',
                        'code'=>403,
                        'info'=>'参数 Server[Send_Email_Fail] 缺失/错误，邮件发送失败',
                    );
                    // 输出数据
                    $ApiFunction->logs('mailer_code','邮件发送',0,'Server_SendEmailFail');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                // 编译数据
                $data = array(
                    'output'=>'UPLOAD_FAIL',
                    'code'=>403,
                    'info'=>'参数 Server[upload_fail] 缺失/错误，数据库上传失败',
                );
                // 输出数据
                $ApiFunction->logs('mailer_code','邮件发送',0,'Server_SqlUploadFail');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'CODE_EFFECTIVE',
                'code'=>200,
                'info'=>'您的验证码依旧有效',
                'data'=>array(
                    'code'=>$Result_Captcha_Object->code,
                    'time'=>date("Y-m-d H:i:s",$Result_Captcha_Object->time+$setting['CAPTCHA_TIME']),
                )
            );
            // 输出数据
            $ApiFunction->logs('mailer_ code','邮件发送',1);
        }
    } elseif (!empty($PostData['data']['P_phone']) and empty($PostData['data']['P_email']) and preg_match('/^(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8}$/', $PostData['data']['P_phone'])) {

    } else {
        // 编译数据
        $data = array(
            'output'=>'EMAIL/PHONE_ERROR',
            'code'=>403,
            'info'=>'参数 Post[P_email/P_phone] 缺失/错误',
        );
        // 输出数据
        $ApiFunction->logs('mailer_code','用户检查',0,'Post_P_email/P_phone');
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
    $ApiFunction->logs('mailer_code','密钥检查',0,'Post_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);