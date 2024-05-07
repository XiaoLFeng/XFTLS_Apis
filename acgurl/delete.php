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
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Key.php';
$ApiFunction = new ApiFunction();
$Key = new Key();
$Check = new Data_Check();

// 获取参数
// POST
$GetPost = file_get_contents('php://input');
$PostData = json_decode($GetPost,true);

// 函数配置
if ($ApiFunction->Check_Ukey($PostData['ukey'])) {
    // 获取数据库信息
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$PostData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    if ($Result_User_Object->id != null) {
        // 验证数据的可行性
        if ($Check->Service_Acgurl_Delete($PostData['data']['P_key'])) {
            $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE access_key='".$PostData['data']['P_key']."'");
            $Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl);

            if ($Result_Acgurl_Object->id != null) {
                if ($Result_Acgurl_Object->uid == $Result_User_Object->id) {
                    if (mysqli_query($SqlConn,"DELETE FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE access_key='".$PostData['data']['P_key']."' AND uid='".$Result_User_Object->id."'")) {
                        // 编译数据
                        $data = array(
                            'output'=>'SUCCESS',
                            'code'=>200,
                            'info'=>'图库已删除'
                        );
                        // 输出数据
                        $ApiFunction->logs('service_acgurl_delete','图库删除',1);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'UPLOAD_FAIL',
                            'code'=>403,
                            'info'=>'参数 Server[Upload] 错误'
                        );
                        // 输出数据
                        $ApiFunction->logs('service_acgurl_create','数据上传',0,'Server_Upload_Fail');
                        header("HTTP/1.1 403 Forbidden");
                    }
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'IMAGE_NOT_YOUR',
                        'code'=>403,
                        'info'=>'这个图库不是你的'
                    );
                    // 输出数据
                    $ApiFunction->logs('service_acgurl_delete','图库删除',0,'Server_Delete_Fail[Image_Not_Your]');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                // 编译数据
                $data = array(
                    'output'=>'IMAGE_NONE',
                    'code'=>403,
                    'info'=>'没有这个图库'
                );
                // 输出数据
                $ApiFunction->logs('service_acgurl_delete','图库删除',0,'Server_Delete_Fail[Image_None]');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>$Check->Service_Acgurl_Delete($PostData['data']['P_key']),
                'code'=>403,
                'info'=>'参数 Post[name/open] 缺失/错误，错误参数：'.$Check->Service_Acgurl_Delete($PostData['data']['P_key']),
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl_delete','参数错误',0,'Post_ukey['.$Check->Service_Acgurl_Delete($PostData['data']['P_key']).']');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'USER_NONE',
            'code'=>403,
            'info'=>'参数 Post[ukey] 缺失/错误，没有这个用户'
        );
        // 输出数据
        $ApiFunction->logs('service_acgurl_delete','没有用户',0,'Post_ukey[User_None]');
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
    $ApiFunction->logs('service_acgurl_delete','密钥验证',0,'Post_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);