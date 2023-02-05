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
// GET
$GetData = array(
    'ukey'=>urldecode(htmlspecialchars($_GET['ukey'])),
    'uid'=>urldecode(htmlspecialchars($_GET['uid'])),
    'email'=>urldecode(htmlspecialchars($_GET['email'])),
);

// 函数配置
if ($ApiFunction->Check_Ukey($GetData['ukey'])) {
    // 检查输出
    if (!empty($GetData['uid']) and empty($GetData['email'])) {
        // 数据库检索
        $Result_Avatar = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['avatar']." WHERE `id`='".$GetData['uid']."'");
        $data = FC_Avatar($Result_Avatar, $ApiFunction);
    } elseif (empty($GetData['uid']) and !empty($GetData['email'])) {
            // 数据库检索
            $Result_Avatar = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." a RIGHT JOIN ".$setting['TABLE']['avatar']." b ON a.id=b.uid WHERE email='".$GetData['email']."'");
        $data = FC_Avatar($Result_Avatar, $ApiFunction);
    } else {
        // 编译数据
        $data = array(
            'output'=>'DATA_ERROR',
            'code'=>403,
            'info'=>'参数 Query[uid/email] 缺失/错误'
        );
        // 输出数据
        $ApiFunction->logs('avatar_get','参数错误',0,'Query_uid/email');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'UEKY_DENY',
        'code'=>403,
        'info'=>'参数 Query[ukey] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('avatar_get','密钥获取',0,'Query_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);

/**
 * @param $Result_Avatar
 * @param ApiFunction $ApiFunction
 * @return array
 */
function FC_Avatar($Result_Avatar, ApiFunction $ApiFunction): array
{
    $Result_Avatar_Object = mysqli_fetch_object($Result_Avatar);
    // 输出检索
    if ($Result_Avatar_Object->id != null) {
        // 编译数据
        $data = array(
            'output' => 'SUCCESS',
            'code' => 200,
            'info' => '数据输出成功',
            'data' => array(
                'id' => $Result_Avatar_Object->id,
                'url' => $Result_Avatar_Object->icon,
                'icon' => 'https://api.x-lf.cn/avatar/?uid=' . $Result_Avatar_Object->id,
            ),
        );
        // 输出数据
        $ApiFunction->logs('avatar_get', '输出头像', 1);
    } else {
        // 编译数据
        $data = array(
            'output' => 'AVATAR_NONE',
            'code' => 200,
            'info' => '暂无头像',
        );
        // 输出数据
        $ApiFunction->logs('avatar_get', '输出头像', 0, 'Server_None');
    }
    return $data;
}