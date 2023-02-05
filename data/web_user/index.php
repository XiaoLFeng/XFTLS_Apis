<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/** @var mysqli $SqlConn 数据库链接参数 */
/** @var array $setting 参数配置
 * @var array $data 数组转Json */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
$ApiFunction = new ApiFunction();

// 获取参数
$GetUid = htmlspecialchars($_GET['uid']);
$GetUserName = htmlspecialchars($_GET['username']);
$GetEmail = htmlspecialchars($_GET['email']);
$GetSSID = htmlspecialchars($_GET['ssid']);
$GetUkey = htmlspecialchars($_GET['ukey']);

// 函数构建
if ($ApiFunction->Check_Session($GetSSID)) {
    // 数据库提取信息
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ".use_type());
    $Result_User_Object = mysqli_fetch_object($Result_User);

    // 校验是否有该用户
    if ($Result_User_Object->id != null) {
        // 编译数据
        $data = array(
            'output'=>'SUCCESS',
            'code'=>200,
            'info'=>'数据输出成功',
            'data'=>array(
                'normal_info'=>array(
                    'id'=>$Result_User_Object->id,
                    'email'=>$Result_User_Object->email,
                    'username'=>$Result_User_Object->username,
                    'displayname'=>$Result_User_Object->displayname,
                ),
                'important'=>array(
                    'ukey'=>$Result_User_Object->ukey,
                    'permission'=>$Result_User_Object->permission,
                    'regip'=>$Result_User_Object->regip,
                    'lastip'=>$Result_User_Object->lastip,
                    'regtime'=>$Result_User_Object->regtime,
                    'lastlogin'=>$Result_User_Object->lastlogin,
                    'ban'=>$Result_User_Object->ban,
                ),
                'social_contact_info'=>array(
                    'website'=>$Result_User_Object->website,
                    'desc'=>$Result_User_Object->desc,
                    'qq'=>$Result_User_Object->qq,
                ),
            ),
        );
        // 输出数据
        $ApiFunction->logs('user_search','查询用户',1);
    } else {
        // 编译数据
        $data = array(
            'output'=>'USER_NONE',
            'code'=>403,
            'info'=>'参数 Query[uid/username/email/ukey] 无用户',
        );
        // 输出数据
        $ApiFunction->logs('user_search','查询用户',0,'Query_uid/username/email');
        header("HTTP/1.1 403 Forbidden");
    }

    // 处理数据库
    mysqli_free_result($Result_User);
    mysqli_close($SqlConn);
} else {
    // 编译数据
    $data = array(
        'output'=>'SSID_DENY',
        'code'=>403,
        'info'=>'参数 Query[ssid] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('user_search','查询用户',0,'Query_ssid');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);

function use_type(): string {
    global $GetUid,$GetEmail,$GetUserName,$GetUkey;
    if (!empty($GetUid) and empty($GetUserName) and empty($GetEmail) and empty($GetUkey)) {
        return "id='$GetUid'";
    } elseif (empty($GetUid) and !empty($GetUserName) and empty($GetEmail) and empty($GetUkey)) {
        return "username='$GetUserName'";
    } elseif (empty($GetUid) and empty($GetUserName) and !empty($GetEmail) and empty($GetUkey)) {
        return "email='$GetEmail'";
    } elseif (empty($GetUid) and empty($GetUserName) and empty($GetEmail) and !empty($GetUkey)) {
        return "ukey='$GetUkey'";
    } else {
        return "id='NULL'";
    }
}