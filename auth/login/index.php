<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var mysqli $SqlConn 数据库链接参数
 * @var array $setting 参数配置
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/User.php';
$ApiFunction = new ApiFunction();
$User = new User();

// 获取参数
// POST
$GetPost = file_get_contents('php://input');
$PostData = json_decode($GetPost,true);
// GET
$GetData = array(
    'ukey'=>urldecode(htmlspecialchars($_GET['ukey'])),
    'email'=>urldecode(htmlspecialchars($_GET['email'])),
    'username'=>urldecode(htmlspecialchars($_GET['username'])),
    'password'=>urldecode(htmlspecialchars($_GET['password'])),
);

// 函数构建
// POST方法
if ($ApiFunction->Get_ukey($PostData['ukey'])) {
    // 检查用户输入内容
    if ($User->login_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password']) == 'TRUE') {
        // 获取数据库信息
        $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ".type_post());
        $Result_User_Object = mysqli_fetch_object($Result_User);

        // 确认是否有该用户
        if ($Result_User_Object->id != null) {
            // 验证用户输入内容是否正确
            if (password_verify($PostData['data']['P_password'],$Result_User_Object->password)) {
                // 编译数据
                $data = array(
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'数据验证成功'
                );
                // 输出数据
                $ApiFunction->logs('api_auth','用户登录',1);
            } else {
                // 编译数据
                $data = array(
                    'output'=>'PASSWORD_DENY',
                    'code'=>403,
                    'info'=>'参数 Post[P_password] 错误'
                );
                // 输出数据
                $ApiFunction->logs('api_auth','用户登录',0,'Post_P_password');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'USER_NONE',
                'code'=>403,
                'info'=>'参数 Post[P_email/P_username] 无用户',
            );
            // 输出数据
            $ApiFunction->logs('api_auth','用户登录',0,'Post_P_email/P_username');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>$User->login_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password']),
            'code'=>403,
            'info'=>'参数 Post[P_email/P_username] 缺失/错误（返回结果：'.$User->login_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password']).'）'
        );
        // 输出数据
        $ApiFunction->logs('api_auth','用户登录',0,'Post_P_email/P_username['.$User->login_input_data($PostData['data']['P_email'],$PostData['data']['P_username'],$PostData['data']['P_password']).']');
        header("HTTP/1.1 403 Forbidden");
    }
// GET方法
} elseif ($ApiFunction->Get_ukey($GetData['ukey'])) {
    // 检查用户输入内容
    if ($User->login_input_data($GetData['email'],$GetData['username'],$GetData['password']) == 'TRUE') {
        // 获取数据库信息
        $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ".type_get());
        $Result_User_Object = mysqli_fetch_object($Result_User);

        // 确认是否有该用户
        if ($Result_User_Object->id != null) {
            // 验证用户输入内容是否正确
            if (password_verify($GetData['password'],$Result_User_Object->password)) {
                // 编译数据
                $data = array(
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'数据验证成功'
                );
                // 输出数据
                $ApiFunction->logs('api_auth','用户登录',1);
            } else {
                // 编译数据
                $data = array(
                    'output'=>'PASSWORD_DENY',
                    'code'=>403,
                    'info'=>'参数 Query[password] 错误'
                );
                // 输出数据
                $ApiFunction->logs('api_auth','用户登录',0,'Query_password');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'USER_NONE',
                'code'=>403,
                'info'=>'参数 Query[email/username] 无用户',
            );
            // 输出数据
            $ApiFunction->logs('api_auth','用户登录',0,'Query_email/username');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>$User->login_input_data($GetData['email'],$GetData['username'],$GetData['password']),
            'code'=>403,
            'info'=>'参数 Query[email/username] 缺失/错误（返回结果：'.$User->login_input_data($GetData['email'],$GetData['username'],$GetData['password']).'）'
        );
        // 输出数据
        $ApiFunction->logs('api_auth','用户登录',0,'Query_email/username['.$User->login_input_data($GetData['email'],$GetData['username'],$GetData['password']).']');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'UEKY_DENY',
        'code'=>403,
        'info'=>'参数 All[ukey] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('api_auth','用户登录',0,'All_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);

function type_post(): string {
    global $PostData;
    if (!empty($PostData['data']['P_email']) and empty($PostData['data']['P_username'])) {
        return "email='".$PostData['data']['P_email']."'";
    } elseif (empty($PostData['data']['P_email']) and !empty($PostData['data']['P_username'])) {
        return "username='".$PostData['data']['P_username']."'";
    } else {
        return "username='NULL'";
    }
}

function type_get(): string {
    global $GetData;
    if (!empty($GetData['email']) and empty($GetData['username'])) {
        return "email='".$GetData['email']."'";
    } elseif (empty($GetData['email']) and !empty($GetData['username'])) {
        return "username='".$GetData['username']."'";
    } else {
        return "username='NULL'";
    }
}