<?PHP
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/** @var TYPE_NAME $SqlConn
/** @var TYPE_NAME $setting */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';
header("Content-Type: image/jpeg;text/html; charset=utf-8");

// 获取数据
// GET
$GetData = array(
    'uid'=>urldecode(htmlspecialchars($_GET['uid'])),
    'size'=>urldecode(htmlspecialchars($_GET['size'])),
);

// 函数构建
if (!empty($GetData['uid'])) {
    // 数据库获取
    $Result_avatar = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['avatar']." WHERE uid='".$GetData['uid']."'");
    $Result_avatar_Object = mysqli_fetch_object($Result_avatar);
    // 检查是否存在
    if (isset($Result_avatar_Object->icon)) {
        // 若正确信息返回数据
        $Image_Url = file_get_contents($Result_avatar_Object->icon,true);
    } else {
        // 如果数据错误返回不存在
        $Image_Url = file_get_contents('https://api.x-lf.cn/avatar/no.png',true);
    }
} else {
    // 没有参数返回404
    $Image_Url = file_get_contents('https://api.x-lf.cn/avatar/404.png',true);
}
echo $Image_Url;
exit();

// 处理数据库
mysqli_free_result($Result_avatar);
mysqli_close($sql_conn);