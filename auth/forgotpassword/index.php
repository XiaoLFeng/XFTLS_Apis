<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var mysqli $SqlConn 数据库链接参数
 * @var array $setting
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
// POST方法
if ($ApiFunction->Get_UserSSID($PostData['ssid'])) {
    // 检查用户输入
    if ($User->forgotpassword_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_phone'],$PostData['data']['P_code'],$PostData['data']['P_password']) == 'TRUE') {
        // 获取数据库信息
        $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ".type());
        $Result_User_Object = mysqli_fetch_object($Result_User);

        // 确认是否有该用户
        if ($Result_User_Object->id != null) {
            // 检查验证码是否有效
            $Result_Captcha = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['captcha']." WHERE `email`='".$PostData['data']['P_email']."' AND `type`='forgotpassword' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
            $Result_Captcha_Object = mysqli_fetch_object($Result_Captcha);

            // 检查
            if  ($Result_Captcha_Object->id != null) {
                /*
                 * 修改密码部分
                 */
                // 校验验证码
                if ($PostData['data']['P_code'] == $Result_Captcha_Object->code) {
                    $password = password_hash($PostData['data']['P_password'],PASSWORD_DEFAULT);
                    if (!empty($PostData['data']['P_password']) and mysqli_query($SqlConn,"UPDATE ".$setting['TABLE']['user']." SET `password` = '$password' WHERE ".type())) {
                        // 编译数据
                        $data = array(
                            'output'=>'SUCCESS',
                            'code'=>200,
                            'info'=>'密码修改成功',
                        );
                        // 输出数据
                        mysqli_query($SqlConn,"UPDATE ".$setting['TABLE']['captcha']." SET `use`='TRUE' WHERE `email`='".$PostData['data']['P_email']."' AND `type`='forgotpassword' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
                        $Mail->Mailer('forgot_check',$PostData['data']['P_email']);
                        $ApiFunction->logs('api_forgotpassword','密码重置',1);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'CHANGE_FAIL',
                            'code'=>403,
                            'info'=>'参数 Post[P_password] 缺失/错误，密码修改失败',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_forgotpassword','重置密码失败',0,'Server_change|Post_P_password');
                        header("HTTP/1.1 403 Forbidden");
                    }
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'CAPTCHA_ERROR',
                        'code'=>403,
                        'info'=>'参数 Post[P_code] 缺失/错误',
                    );
                    // 输出数据
                    $ApiFunction->logs('api_forgotpassword','验证码错误',0,'Post_P_code');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                $code = $ApiFunction->Captcha(6);
                if (mysqli_query($SqlConn, "INSERT INTO ".$setting['TABLE']['captcha']." (`email`,`code`,`type`,`time`) VALUES ('".$PostData['data']['P_email']."','".$code."','forgotpassword','".time()."')")) {
                    if ($Mail->Mailer('forgotpassword',$PostData['data']['P_email'],$code,time(),$setting['CAPTCHA_TIME'])) {
                        // 编译数据
                        $data = array(
                            'output'=>'EMAIL_SEND',
                            'code'=>200,
                            'info'=>'重置邮件已发送',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_forgotpassword','邮件发送',1);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'EMAIL_FAIL',
                            'code'=>403,
                            'info'=>'邮件 Email_Fail 发送失败',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_forgotpassword','邮件发送',0,'Email_Fail');
                        header("HTTP/1.1 403 Forbidden");
                    }
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'UPLOAD_FAIL',
                        'code'=>403,
                        'info'=>'服务器 Server[upload] 新建错误',
                    );
                    // 输出数据
                    $ApiFunction->logs('api_forgotpassword','数据上传',0,'Server_upload');
                    header("HTTP/1.1 403 Forbidden");
                }
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'USER_NONE',
                'code'=>200,
                'info'=>'参数 Post[P_email/P_username/P_phone] 缺失/错误',
            );
            // 输出数据
            $ApiFunction->logs('api_forgotpassword','无该用户',0,'Post_P_email/P_username/P_phone');
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>$User->forgotpassword_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_phone'],$PostData['data']['P_code'],$PostData['data']['P_password']),
            'code'=>403,
            'info'=>'参数 POST 缺失/错误，错误原因：'.$User->forgotpassword_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_phone'],$PostData['data']['P_code'],$PostData['data']['P_password']),
        );
        // 输出数据
        $ApiFunction->logs('api_forgotpassword','数据校验',0,'Post['.$User->forgotpassword_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_phone'],$PostData['data']['P_code'],$PostData['data']['P_password']).']');
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'SSID_DENY',
        'code'=>403,
        'info'=>'参数 Post[ssid] 缺失/错误',
    );
    // 输出数据
    $ApiFunction->logs('api_forgotpassword','密钥检查',0,'Post_ssid');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);

function type(): string {
    global $PostData;
    if (!empty($PostData['data']['P_email']) and empty($PostData['data']['P_username']) and empty($PostData['data']['P_phone'])) {
        return "email='".$PostData['data']['P_email']."'";
    } elseif (empty($PostData['data']['P_email']) and !empty($PostData['data']['P_username']) and empty($PostData['data']['P_phone'])) {
        return "username='".$PostData['data']['P_username']."'";
    } elseif (empty($PostData['data']['P_email']) and empty($PostData['data']['P_username']) and !empty($PostData['data']['P_phone'])) {
        return "phone='".$PostData['data']['P_phone']."'";
    } else {
        return "username='NULL'";
    }
}