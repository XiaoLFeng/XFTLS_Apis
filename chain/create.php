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
$Check = new Data_Check();
$Key = new Key();

// 获取参数
// POST
$GetPost = file_get_contents('php://input');
$PostData = json_decode($GetPost,true);

// 逻辑构建
if ($ApiFunction->Check_Ukey($PostData['ukey'])) {
    // 查询用户
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$PostData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    // 检查输入地址是否符合规则
    if ($Check->Service_Chain_Create($PostData['data']['P_url'])) {
        // 数据库地址查找
        $Result_URL = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Chain']." WHERE `url`='".addslashes($PostData['data']['P_url'])."'");
        $Result_URL_Object = mysqli_fetch_object($Result_URL);
        if ($Result_URL_Object->id == null) {
            // 获取随机字符并验证是否重复
            $Result_URL = null;         // 格式化数据
            while (true) {
                $rand_string = $Key->Rand_String(5);
                $Result_URL = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Chain']." WHERE `url_id`='$rand_string'");
                $Result_URL_Object = mysqli_fetch_object($Result_URL);
                // 如果没有查询到数据则判定为没有重复内容
                if ($Result_URL_Object->id == null) break;
                $Result_URL = null;         // 格式化数据
            }
            // 输入数据进入数据库
            if (mysqli_query($SqlConn,"INSERT INTO ".$setting['TABLE']['Service']['Chain']." (`user_id`,`url_id`,`url`,`open`,`time`) VALUES ('".$Result_User_Object->id."','".$rand_string."','".$PostData['data']['P_url']."','".$PostData['data']['P_open']."','".time()."')")) {
                // 编译数据
                $data = [
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'创建成功',
                    'data'=>[
                        'url'=>$PostData['data']['P_url'],
                        'url_id'=>$rand_string,
                        'open'=>$PostData['data']['P_open'],
                    ],
                ];
                // 输出数据
                $ApiFunction->logs('service_chain_create','短链创建',1);
            } else {
                // 编译数据
                $data = [
                    'output'=>'UPLOAD_FAIL',
                    'code'=>403,
                    'info'=>'数据创建失败'
                ];
                // 输出数据
                $ApiFunction->logs('service_chain_create','上传失败',0,'Server_UploadFail');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            if (!$Result_User_Object->open) {
                // 编译数据
                $data = [
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'该短链已有创建',
                    'data'=>[
                        'url_id'=>$Result_URL_Object->url_id,
                        'url'=>$Result_URL_Object->url,
                    ],
                ];
                // 输出数据
                $ApiFunction->logs('service_chain_create','重复调用',1);
            } else {
                // 编译数据
                $data = [
                    'output'=>'USER_HIDE',
                    'code'=>200,
                    'info'=>'用户隐藏此短链'
                ];
                // 输出数据
                $ApiFunction->logs('service_chain_create','用户隐藏',1);
            }
        }
    } else {
        // 编译数据
        $data = [
            'output'=>'URL_FALSE',
            'code'=>403,
            'info'=>'参数 Post[url] 缺失/错误，不符合规则'
        ];
        // 输出数据
        $ApiFunction->logs('service_chain_create','格式错误',0,'Post_UrlFalse');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = [
        'output'=>'UKEY_DENY',
        'code'=>403,
        'info'=>'参数 Post[ukey] 缺失/错误'
    ];
    // 输出数据
    $ApiFunction->logs('service_chain_create','密钥检查',0,'Post_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);