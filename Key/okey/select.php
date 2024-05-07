<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var mysqli $SqlConn 数据库链接参数
 * @var array $setting 参数配置
 * @var array $data 数组转Json
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
$ApiFunction = new ApiFunction();

// 获取参数
// GET
$GetData = [
    'ukey'=>urldecode(htmlspecialchars($_GET['ukey'])),
    'password'=>urldecode(htmlspecialchars($_GET['password'])),
];

// 逻辑搭建
if ($ApiFunction->Check_Ukey($GetData['ukey'])) {
    // 检查 Ukey 是否有效
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE `ukey`='".$GetData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    // 密码检查
    if (password_verify($GetData['password'],$Result_User_Object->password)) {
        // 执行筛选
        $Result_Okey = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['okey']." WHERE `user_id`='".$Result_User_Object->id."'");
        $Result_Okey_Object = mysqli_fetch_object($Result_Okey);

        // 检查密钥是否存在
        if ($Result_Okey_Object->id != null) {
            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'数据查询完毕',
                'data'=>[
                    'okey'=>$Result_Okey_Object->okey,
                ]
            );
            // 输出数据
            $ApiFunction->logs('okey_select','数据查询',1);
        } else {
            // 编译数据
            $data = array(
                'output'=>'SSID_NONE',
                'code'=>200,
                'info'=>'没有密钥'
            );
            // 输出数据
            $ApiFunction->logs('okey_select','数据查询',0,'Server_OkeyNone');
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'PASSWORD_DENY',
            'code'=>403,
            'info'=>'参数 Query[password] 错误'
        );
        // 输出数据
        $ApiFunction->logs('okey_select','密码验证',0,'Query_Password');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'UKEY_DENY',
        'code'=>403,
        'info'=>'参数 Query[ukey] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('okey_select','密钥检查',0,'Query_Ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);