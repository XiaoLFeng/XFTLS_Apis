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

// 函数构建
// POST方法
if ($ApiFunction->Get_UserSSID($PostData['ssid'])) {
    // 验证用户输入的内容
    if ($User->unregister($PostData['data']['P_email'],$PostData['data']['P_password'],$PostData['data']['P_code'])) {
        // 查找用户
        $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE `email`='".$PostData['data']['P_email']."'");
        $Result_User_Object = mysqli_fetch_object($Result_User);

        // 确认用户
        if ($Result_User_Object->id != null) {
            // 已有用户则验证验证码
            $Result_Captcha = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['captcha']." WHERE `email`='".$PostData['data']['P_email']."' AND `type`='unregister' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
            $Result_Captcha_Object = mysqli_fetch_object($Result_Captcha);

            // 检查
            if ($Result_Captcha_Object->code != null) {
                // 检查验证码
                if ($PostData['data']['P_code'] == $Result_Captcha_Object->code) {
                    // 验证密码
                    if (password_verify($PostData['data']['P_password'],$Result_User_Object->password)) {
                        if (mysqli_query($SqlConn,"DELETE FROM ".$setting['TABLE']['user']." WHERE `id`=".$Result_User_Object->id)) {
                            // 编译数据
                            $data = array(
                                'output'=>'SUCCESS',
                                'code'=>200,
                                'info'=>'用户已成功注销',
                            );
                            // 输出数据
                            mysqli_query($SqlConn,"UPDATE ".$setting['TABLE']['captcha']." SET `use`='TRUE' WHERE `email`='".$PostData['data']['P_email']."' AND `type`='unregister' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
                            $Mail->Mailer('unregister_over',$PostData['data']['P_email']);
                            $ApiFunction->logs('api_unregister','注销用户',1);
                        } else {
                            // 编译数据
                            $data = array(
                                'output'=>'UNREGISTER_FAIL',
                                'code'=>403,
                                'info'=>'服务器 Server[unregister] 注销失败',
                            );
                            // 输出数据
                            $ApiFunction->logs('api_unregister','注销用户',0,'Server_unregister');
                            header("HTTP/1.1 403 Forbidden");
                        }
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'PASSWORD_ERROR',
                            'code'=>403,
                            'info'=>'参数 Post[P_password] 缺失/错误',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_unregister','密码错误',0,'Post_P_password');
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
                    $ApiFunction->logs('api_unregister','验证码错误',0,'Post_P_code');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                // 发送邮件创建注销方式
                $code = $ApiFunction->Captcha(10,'normal');
                if (mysqli_query($SqlConn, "INSERT INTO ".$setting['TABLE']['captcha']." (`email`,`code`,`type`,`time`) VALUES ('".$PostData['data']['P_email']."','".$code."','unregister','".time()."')")) {
                    if ($Mail->Mailer('unregister',$PostData['data']['P_email'],$code,time(),$setting['CAPTCHA_TIME'])) {
                        // 编译数据
                        $data = array(
                            'output'=>'SUCCESS',
                            'code'=>200,
                            'info'=>'邮件已发送',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_unregister','邮件发送',1);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'Email_Fail',
                            'code'=>403,
                            'info'=>'邮件 Email_Fail 发送失败',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_unregister','邮件发送',0,'Email_Fail');
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
                    $ApiFunction->logs('api_unregister','数据上传',0,'Server_upload');
                    header("HTTP/1.1 403 Forbidden");
                }
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'USER_NONE',
                'code'=>403,
                'info'=>'没有这个用户',
            );
            // 输出数据
            $ApiFunction->logs('api_unregister','用户检查',0,'Post_P_email');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>$User->unregister($PostData['data']['P_email'],$PostData['data']['P_password'],$PostData['data']['P_code']),
            'code'=>403,
            'info'=>'参数 POST 缺失/错误，错误原因：'.$User->unregister($PostData['data']['P_email'],$PostData['data']['P_password'],$PostData['data']['P_code']),
        );
        // 输出数据
        $ApiFunction->logs('api_unregister','数据校验',0,'Post['.$User->unregister($PostData['data']['P_email'],$PostData['data']['P_password'],$PostData['data']['P_code']).']');
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'SSID_DENY',
        'code'=>403,
        'info'=>'参数 Post[ssid] 缺失/错误',
    );
    // 输出数据
    $ApiFunction->logs('api_unregister','密钥检查',0,'Post_ssid');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);