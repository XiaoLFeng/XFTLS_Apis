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
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Data_Check.php';
$ApiFunction = new ApiFunction();
$Check = new Data_Check();

// 获取参数
// GET
$GetData = [
    'ukey'=>urldecode(htmlspecialchars($_GET['ukey'])),
    'url_id'=>urldecode(htmlspecialchars($_GET['url_id'])),
];

// 逻辑构建
if ($ApiFunction->Check_Ukey($GetData['ukey'])) {
    // 查询用户
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$GetData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    // 检查 URL_ID
    if ($Check->Service_Chain_Select($GetData['url_id'])) {
        // 查找数据
        $Result_URL = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Chain']." WHERE `url_id`='".$GetData['url_id']."'");
        $Result_URL_Object = mysqli_fetch_object($Result_URL);
        if ($Result_URL_Object->id != null) {
            if ($Result_User_Object->id == $Result_URL_Object->user_id) {
                if (mysqli_query($SqlConn,"DELETE FROM ".$setting['TABLE']['Service']['Chain']." WHERE `url_id`='".$GetData['url_id']."'")) {
                    // 编译数据
                    $data = [
                        'output'=>'SUCCESS',
                        'code'=>200,
                        'info'=>'删除成功',
                    ];
                    // 输出数据
                    $ApiFunction->logs('service_chain_delete','短链删除',1);
                } else {
                    // 编译数据
                    $data = [
                        'output'=>'UPLOAD_FAIL',
                        'code'=>403,
                        'info'=>'数据创建失败'
                    ];
                    // 输出数据
                    $ApiFunction->logs('service_chain_delete','删除失败',0,'Server_UploadFail');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                // 编译数据
                $data = [
                    'output'=>'URL_ID_NOT_YOUR',
                    'code'=>403,
                    'info'=>'这不是你的'
                ];
                // 输出数据
                $ApiFunction->logs('service_chain_delete','非此用户',0,'Server_UrlIdNotYour');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = [
                'output'=>'URL_ID_NONE',
                'code'=>403,
                'info'=>'没有这个链接'
            ];
            // 输出数据
            $ApiFunction->logs('service_chain_delete','非此用户',0,'Server_UrlIdNone');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = [
            'output'=>'URL_ID_FAIL',
            'code'=>403,
            'info'=>'参数 Query[url_id] 缺失/错误'
        ];
        // 输出数据
        $ApiFunction->logs('service_chain_delete','短链查询',0,'Query_UrlIdFail');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = [
        'output'=>'UKEY_DENY',
        'code'=>403,
        'info'=>'参数 Query[ukey] 缺失/错误'
    ];
    // 输出数据
    $ApiFunction->logs('service_chain_delete','用户认证',0,'Query_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);