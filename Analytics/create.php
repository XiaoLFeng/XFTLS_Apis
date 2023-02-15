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
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Key.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Data_Check.php';
$ApiFunction = new ApiFunction();
$Key = new Key();
$Check = new Data_Check();

// 获取参数
// POST
$GetPost = file_get_contents('php://input');
$PostData = json_decode($GetPost,true);

// 逻辑构建
if ($ApiFunction->Check_Ukey($PostData['ukey'])) {
    // 搜索用户
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE `ukey`='".$PostData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    // 检查输入数据
    if ($Check->Service_Analytics_Create($PostData['data']['P_displayname'],$PostData['data']['P_domain'],$PostData['data']['P_open'])) {
        // 输入数据
        if (mysqli_query($SqlConn,"INSERT INTO ".$setting['TABLE']['Service']['Analytics']." (`uid`,`sid`,`displayname`,`domain`,`open`) VALUES ('".$Result_User_Object->id."','".$Key->service_analytics_create(5)."','".$PostData['data']['P_displayname']."','".$PostData['data']['P_domain']."','".$PostData['data']['P_open']."')")) {
            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'创建成功'
            );
            // 输出数据
            $ApiFunction->logs('service_analytics_create','网站分析',1);
        } else {
            // 编译数据
            $data = array(
                'output'=>'UPLOAD_FAIL',
                'code'=>403,
                'info'=>'数据创建失败'
            );
            // 输出数据
            $ApiFunction->logs('service_analytics_create','网站分析',0,'Server_UploadFail');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>$Check->Service_Analytics_Create($PostData['data']['P_displayname'],$PostData['data']['P_domain'],$PostData['data']['P_open']),
            'code'=>403,
            'info'=>'参数 Post[displayname/domain/open] 缺失/错误，错误参数：'.$Check->Service_Analytics_Create($PostData['data']['P_displayname'],$PostData['data']['P_domain'],$PostData['data']['P_open']),
        );
        // 输出数据
        $ApiFunction->logs('service_analytics_create','参数错误',0,'Post_data['.$Check->Service_Analytics_Create($PostData['data']['P_displayname'],$PostData['data']['P_domain'],$PostData['data']['P_open']).']');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'UEKY_DENY',
        'code'=>403,
        'info'=>'参数 Post[ukey] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('service_analytics_create','密钥验证',0,'Post_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);