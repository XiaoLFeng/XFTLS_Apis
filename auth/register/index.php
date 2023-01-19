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
    // 验证数据的有效性
    if ($User->register_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password'],$PostData['data']['P_desc'],$PostData['data']['P_regip']) == 'TRUE') {
        // 数据库检索用户信息
        $Result_UserEMail = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE email='".$PostData['data']['P_email']."'");
        $Result_UserUsername = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE username='".$PostData['data']['P_username']."'");
        $Result_UserEMail_Object = mysqli_fetch_object($Result_UserEMail);
        $Result_UserUsername_Object = mysqli_fetch_object($Result_UserUsername);

        // 验证是否有用户
        if ($Result_UserEMail_Object->id == null and $Result_UserUsername_Object->id == null) {
            // 检查验证码
            // 已有用户则验证验证码
            $Result_Captcha = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['captcha']." WHERE `email`='".$PostData['data']['P_email']."' AND `type`='register' AND `time`>='".(time()-$setting['CAPTCHA_TIME'])."' AND `use`='FALSE'");
            $Result_Captcha_Object = mysqli_fetch_object($Result_Captcha);

            // 检查
            if ($Result_Captcha_Object->code != null) {
                if ($PostData['data']['P_code'] == $Result_Captcha_Object->code) {
                    // 编译数据
                    $Ready_Data = array(
                        'email' => $PostData['data']['P_email'],
                        'username' => $PostData['data']['P_username'],
                        'password' => password_hash($PostData['data']['P_password'], PASSWORD_DEFAULT),
                        'desc' => $PostData['data']['P_desc'],
                        'regip' => $PostData['data']['P_regip'],
                        'regtime' => date('Y-m-d'),
                        'ukey' => $ApiFunction->ukey_create(10),
                    );

                    // 输入数据进入数据库
                    if (mysqli_query($SqlConn, "INSERT INTO " . $setting['TABLE']['user'] . " (`username`,`email`,`password`,`ukey`,`desc`,`regip`,`regtime`) VALUES ('" . $Ready_Data['username'] . "','" . $Ready_Data['email'] . "','" . $Ready_Data['password'] . "','" . $Ready_Data['ukey'] . "','" . $Ready_Data['desc'] . "','" . $Ready_Data['regip'] . "','" . $Ready_Data['regtime'] . "')")) {
                        $data = array(
                            'output' => 'SUCCESS',
                            'code' => 200,
                            'info' => '服务器 Server[upload] 用户新建成功',
                        );
                        // 输出数据
                        $Mail->Mailer('register_over', $Ready_Data['email']);
                        $ApiFunction->logs('api_register', '新建用户', 1);
                    } else {
                        $data = array(
                            'output' => 'UPLOAD_FAIL',
                            'code' => 403,
                            'info' => '服务器 Server[upload] 新建错误',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_register', '数据上传', 0, 'Server_upload');
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
                    $ApiFunction->logs('api_register','验证码错误',0,'Post_P_code');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                // 发送邮件创建注销方式
                $code = $ApiFunction->Captcha(6,'normal');
                if (mysqli_query($SqlConn, "INSERT INTO ".$setting['TABLE']['captcha']." (`email`,`code`,`type`,`time`) VALUES ('".$PostData['data']['P_email']."','".$code."','register','".time()."')")) {
                    if ($Mail->Mailer('register',$PostData['data']['P_email'],$code,time(),$setting['CAPTCHA_TIME'])) {
                        // 编译数据
                        $data = array(
                            'output'=>'SUCCESS',
                            'code'=>200,
                            'info'=>'邮件已发送',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_register','邮件发送',1);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'Email_Fail',
                            'code'=>403,
                            'info'=>'邮件 Email_Fail 发送失败',
                        );
                        // 输出数据
                        $ApiFunction->logs('api_register','邮件发送',0,'Email_Fail');
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
                    $ApiFunction->logs('api_register','数据上传',0,'Server_upload');
                    header("HTTP/1.1 403 Forbidden");
                }
            }
        } else {
            $data = array(
                'output'=>'ALREADY_USER',
                'code'=>200,
                'info'=>'参数 Post[P_email/P_username] 已经有该用户了',
            );
            // 输出数据
            $ApiFunction->logs('api_register','已有用户',0,'Post_P_email/P_username');
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>$User->register_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password'],$PostData['data']['P_desc'],$PostData['data']['P_regip']),
            'code'=>403,
            'info'=>'参数 POST[P_email/P_username/P_password] 缺失/错误，错误类型：'.$User->register_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password'],$PostData['data']['P_desc'],$PostData['data']['P_regip'])
        );
        // 输出数据
        $ApiFunction->logs('api_register','数据无效',0,'Post_P_email/P_username/P_password['.$User->register_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password'],$PostData['data']['P_desc'],$PostData['data']['P_regip']).']');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'SSID_DENY',
        'code'=>403,
        'info'=>'参数 Post[ssid] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('api_register','密钥检查',0,'Post_ssid');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);