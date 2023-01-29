<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var String $SqlConn
 * @var String $setting
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
    'type'=>urldecode(htmlspecialchars($_GET['type'])),
    'key'=>urldecode(htmlspecialchars($_GET['key'])),
);

// 函数构建
if ($ApiFunction->Get_ukey($GetData['ukey'])) {
    // 获取数据库信息
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$GetData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);
    if ($Result_User_Object->id != null) {
        if ($GetData['type'] == 'all') {
            $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE uid='".$Result_User_Object->id."' ORDER BY id DESC");
            $number = 1;
            while ($Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl)) {
                $array[$number] = [
                    'name'=>$Result_Acgurl_Object->name,
                    'access_key'=>$Result_Acgurl_Object->access_key,
                    'open'=>$Result_Acgurl_Object->open,
                ];
                $number ++;
            }
            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'数据输出成功',
                'data'=>$array,
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl_user','获取图库',1);
        } elseif ($GetData['type'] == 'single') {
            // 正则表达式判断
            if (preg_match('/^[0-9A-Za-z]+$/',$GetData['key'],$key)) {
                $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE access_key='".$key[0]."'");
                $number = 1;
                while ($Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl)) {
                    $array[$number] = [
                        'name'=>$Result_Acgurl_Object->name,
                        'access_key'=>$Result_Acgurl_Object->access_key,
                        'url'=>$Result_Acgurl_Object->url,
                        'open'=>$Result_Acgurl_Object->open,
                        'manage'=>[
                            'domain_open'=>$Result_Acgurl_Object->domain_open,
                            'domain_list'=>$Result_Acgurl_Object->domain,
                            'blacklist_open'=>$Result_Acgurl_Object->blacklist_open,
                            'blacklist_list'=>$Result_Acgurl_Object->blacklist,
                        ],
                    ];
                    $number ++;
                }
                // 编译数据
                $data = array(
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'数据输出成功',
                    'data'=>$array,
                );
                // 输出数据
                $ApiFunction->logs('service_acgurl_user','获取图库',1);
            } else {
                // 编译数据
                $data = array(
                    'output'=>'KEY_FALSE',
                    'code'=>403,
                    'info'=>'参数 Query[key] 错误/缺失，不符合规范'
                );
                // 输出数据
                $ApiFunction->logs('service_acgurl_user','密钥验证',0,'Query_key');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'TYPE_ERROR',
                'code'=>403,
                'info'=>'参数 Query[type] 错误/缺失，不存在此类型'
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl_user','密钥验证',0,'Query_type');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'USER_NONE',
            'code'=>403,
            'info'=>'参数 Query[ukey] 没有这个用户'
        );
        // 输出数据
        $ApiFunction->logs('service_acgurl_user','密钥验证',0,'Query_ukey[User_None]');
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
    $ApiFunction->logs('service_acgurl_user','密钥验证',0,'Query_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);