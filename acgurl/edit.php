<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var mysqli $SqlConn 数据库链接参数
 * @var string $setting 配置文件
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
if ($ApiFunction->Get_ukey($PostData['ukey'])) {
    // 获取数据库信息
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$PostData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    if ($Result_User_Object->id != null) {
        // 验证数据的可行性
        if ($Check->Service_Acgurl_Edit($PostData['data']['P_key'],$PostData['data']['P_name'],$PostData['data']['P_url'],$PostData['data']['P_open']) == 'TRUE') {
            $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE `access_key`='".$PostData['data']['P_key']."'");
            $Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl);

            if ($Result_Acgurl_Object->uid == $Result_User_Object->id) {
                if (mysqli_query($SqlConn,"UPDATE ".$setting['TABLE']['Service']['Acgurl']." SET name='".$PostData['data']['P_name']."', url='".$PostData['data']['P_url']."', open=".$PostData['data']['P_open']." WHERE `access_key`='".$PostData['data']['P_key']."'")) {
                    // 编译数据
                    $data = array(
                        'output'=>'SUCCESS',
                        'code'=>200,
                        'info'=>'图库已修改'
                    );
                    // 输出数据
                    $ApiFunction->logs('service_acgurl_edit','图库修改',1);
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'UPLOAD_FAIL',
                        'code'=>403,
                        'info'=>'参数 Server[Upload] 错误'
                    );
                    // 输出数据
                    $ApiFunction->logs('service_acgurl_edit','数据上传',0,'Server_Upload_Fail');
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
                $ApiFunction->logs('service_acgurl_edit','图库修改',0,'Server_Delete_Fail[Image_Not_Your]');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>$Check->Service_Acgurl_Edit($PostData['data']['P_key'],$PostData['data']['P_name'],$PostData['data']['P_url'],$PostData['data']['P_open']),
                'code'=>403,
                'info'=>'参数 Post[name/open] 缺失/错误，错误参数：'.$Check->Service_Acgurl_Edit($PostData['data']['P_key'],$PostData['data']['P_name'],$PostData['data']['P_url'],$PostData['data']['P_open']),
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl_edit','参数错误',0,'Post_ukey['.$Check->Service_Acgurl_Edit($PostData['data']['P_key'],$PostData['data']['P_name'],$PostData['data']['P_url'],$PostData['data']['P_open']).']');
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
        $ApiFunction->logs('service_acgurl_edit','没有用户',0,'Post_ukey[User_None]');
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
    $ApiFunction->logs('service_acgurl_edit','密钥验证',0,'Post_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);