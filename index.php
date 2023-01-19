<?PHP 
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

// 载入头
include($_SERVER['DOCUMENT_ROOT'].'/header-control.php');

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
$ApiFunction = new ApiFunction();

// 编译数据
$data = array(
    'output'=>'SUCCESS',
    'code'=>200,
    'info'=>'当看到此页面代表所有API项目正常运行'
);

// 输出数据
$ApiFunction->logs('api_status','基础调用',1);
echo json_encode($data,JSON_UNESCAPED_UNICODE);