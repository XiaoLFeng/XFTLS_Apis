<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var string $SqlConn
 * @var string $setting
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/User.php';
$ApiFunction = new ApiFunction();
$User = new User();

// 获取参数
// GET
$GetData = array(
    'key'=>urldecode(htmlspecialchars($_GET['key'])),
    'type'=>urldecode(htmlspecialchars($_GET['type'])),
);

if (!empty($GetData['key'])) {
    $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE access_key='".$GetData['key']."'");
    $Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl);
    if ($Result_Acgurl_Object->id != null) {
        if ($GetData['type'] == 'json') {
            $url_data = explode(PHP_EOL,$Result_Acgurl_Object->url);
            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'数据输出完毕',
                'data'=>[
                    'url'=>str_replace(array("\r\n", "\r", "\n"),'',$url_data[array_rand($url_data)]),
                ],
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl','随机图库_Json',1);
        } else {
            header("Content-Type: image/jpeg;text/html; charset=utf-8");
            $url_data = explode(PHP_EOL,$Result_Acgurl_Object->url);
            $Image_Url = file_get_contents(str_replace(array("\r\n", "\r", "\n"),'',$url_data[array_rand($url_data)]),true);
            echo $Image_Url;
            // 输出数据
            $ApiFunction->logs('service_acgurl','随机图库_Url',1);
            exit();
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'KEY_DENY',
            'code'=>403,
            'info'=>'参数 Query[key] 错误，没有这个序列',
        );
        // 输出数据
        $ApiFunction->logs('service_acgurl','序列查找',0,'Query_key');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'KEY_NONE',
        'code'=>403,
        'info'=>'参数 Query[key] 缺失',
    );
    // 输出数据
    $ApiFunction->logs('service_acgurl','序列查找',0,'Query_key');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);