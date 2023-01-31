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
$ApiFunction = new ApiFunction();

// 获取参数
$GetSSID = urldecode(htmlspecialchars($_GET['ssid']));

// 函数构建
if ($GetSSID == $ApiFunction->Get_SSID()) {
    // 从数据库获取信息
    $Result_Info = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['info']." ORDER BY id");
    while ($Result_Info_Object = mysqli_fetch_object($Result_Info)) {
        // 隐藏密钥不输出
        if ($Result_Info_Object->info != 'session') {
            // 除了密钥输出内容
            $data_sql[$Result_Info_Object->info] = array(
                'id'=>$Result_Info_Object->id,
                'text'=>$Result_Info_Object->text,
                'parameter'=>$Result_Info_Object->parameter,
            );
        }
    }
    // 编译数据
    $data = array(
        'output'=>'SUCCESS',
        'code'=>200,
        'info'=>'数据成功输出',
        'data'=>$data_sql,
    );
    // 输出数据
    $ApiFunction->logs('web_info','基础参数',1);
    // 处理数据库
    mysqli_free_result($Result_Info);
    mysqli_close($SqlConn);
} else {
    // 编译数据
    $data = array(
        'output'=>'SSID_DENY',
        'code'=>403,
        'info'=>'参数 Query[ssid] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('web_info','基础参数',0,'Query_ssid');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);