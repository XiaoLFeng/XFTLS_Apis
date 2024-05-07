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
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Mailer.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Key.php';
$ApiFunction = new ApiFunction();
$Mail = new SendMail();
$Key = new Key();

// 获取参数
// POST
$PostData = file_get_contents('php://input');
$PostData = json_decode($PostData,true);

// 函数构建
if ($ApiFunction->Check_Session($PostData['session'])) {
    // 判断 UKey
    if ($ApiFunction->Check_Ukey($PostData['data']['P_ukey'])) {
        // 数据库获取数据
        $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$PostData['data']['P_ukey']."'");
        $Result_User_Object = mysqli_fetch_object($Result_User);
        $Result_SSID = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user_ssid']." WHERE `user_id`='".$Result_User_Object->id."'");
        $Result_SSID_Object = mysqli_fetch_object($Result_SSID);

        // 判断是否存在
        if ($Result_SSID_Object->id == null) {
            $create_ssid = $Key->ssid_create(10);
            if (mysqli_query($SqlConn,"INSERT INTO ".$setting['TABLE']['user_ssid']." (`user_id`,`ssid`,`time`) VALUES ('".$Result_User_Object->id."','".$create_ssid."','".date('Y-m-d H:i:s')."')")) {
                // 编译数据
                $data = array(
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'创建成功'
                );
                // 输出数据
                $Mail->Mailer('ssid_create',$Result_User_Object->email,$create_ssid);
                $ApiFunction->logs('ssid_create','创建密钥',1);
            } else {
                // 编译数据
                $data = array(
                    'output'=>'UPLOAD_FAIL',
                    'code'=>403,
                    'info'=>'上传失败'
                );
                // 输出数据
                $ApiFunction->logs('ssid_create','创建密钥',0,'Server_UploadFail');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'SSID_ALREADY',
                'code'=>403,
                'info'=>'已有密钥'
            );
            // 输出数据
            $ApiFunction->logs('ssid_create','创建密钥',0,'Server_SSIDAlready');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'UKEY_DENY',
            'code'=>403,
            'info'=>'参数 Post[ukey] 缺失/错误'
        );
        // 输出数据
        $ApiFunction->logs('ssid_create','用户ukey',0,'Post_ukey');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'SESSION_DENY',
        'code'=>403,
        'info'=>'参数 Post[session] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('ssid_create','密钥检查',0,'Post_ssid');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);