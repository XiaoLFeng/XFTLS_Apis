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
$ApiFunction = new ApiFunction();
$Check = new Data_Check();

// 获取参数
// GET
$GetData = [
    'url_id'=>urldecode(htmlspecialchars($_GET['url_id'])),
    'ukey'=>urldecode(htmlspecialchars($_GET['ukey'])),
    'type'=>urldecode(htmlspecialchars($_GET['type'])),
];

// 函数构建
if ($Check->Service_Chain_Select($GetData['url_id']) or ($ApiFunction->Check_Ukey($GetData['ukey']) and $GetData['type'] == 'all')) {
    if ($GetData['type'] == 'all') {         // 全数据查询
        // 查询用户
        $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE ukey='".$GetData['ukey']."'");
        $Result_User_Object = mysqli_fetch_object($Result_User);

        if ($ApiFunction->Check_Ukey($GetData['ukey'])) {
            $number = 0;
            // 数据库查询
            $Result_URL = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Chain']." WHERE `user_id`='".$Result_User_Object->id."' ORDER BY `time` DESC");
            while ($Result_URL_Object = mysqli_fetch_object($Result_URL)) {
                $array[$number] = $Result_URL_Object;
                $number ++;
            }

            // 编译数据
            $data = [
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'数据输出成果',
                'data'=>$array,
            ];
        } else {
            // 编译数据
            $data = [
                'output'=>'UKEY_DENY',
                'code'=>403,
                'info'=>'参数 Query[ukey] 缺失/错误'
            ];
            // 输出数据
            $ApiFunction->logs('service_chain_reset','用户认证',0,'Query_ukey');
            header("HTTP/1.1 403 Forbidden");
        }
    } elseif ($GetData['type'] == 'single') {        // 单数据查询
        // 数据库查询
        $Result_URL = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Chain']." WHERE `url_id`='".$GetData['url_id']."'");
        $Result_URL_Object = mysqli_fetch_object($Result_URL);

        if ($Result_URL_Object->id != null) {
            if ($ApiFunction->Check_Ukey($GetData['ukey'])) {
                // 编译数据
                $data = [
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'数据输出成果',
                    'data'=>[
                        'url_id'=>$Result_URL_Object->url_id,
                        'user_id'=>$Result_URL_Object->user_id,
                        'url'=>$Result_URL_Object->url,
                        'open'=>$Result_URL_Object->open,
                        'time'=>$Result_URL_Object->time,
                    ],
                ];
            } else {
                if ($Result_URL_Object->open) {
                    // 编译数据
                    $data = [
                        'output'=>'SUCCESS',
                        'code'=>200,
                        'info'=>'数据输出成果',
                        'data'=>[
                            'url_id'=>$Result_URL_Object->url_id,
                            'url'=>$Result_URL_Object->url,
                        ],
                    ];
                } else {
                    // 编译数据
                    $data = [
                        'output'=>'URL_ID_HIDE',
                        'code'=>200,
                        'info'=>'数据隐藏',
                    ];
                }
            }
            // 输出数据
            $ApiFunction->logs('service_chain_select','短链查询',1);
        } else {
            // 编译数据
            $data = [
                'output'=>'URL_ID_NONE',
                'code'=>200,
                'info'=>'没有短链'
            ];
            // 输出数据
            $ApiFunction->logs('service_chain_select','没有短链',1);
        }
    } else {
        // 编译数据
        $data = [
            'output'=>'TYPE_ERROR',
            'code'=>403,
            'info'=>'参数 Query[type] 缺失/错误'
        ];
        // 输出数据
        $ApiFunction->logs('service_chain_select','短链查询',0,'Query_type');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = [
        'output'=>'URL_ID_FAIL',
        'code'=>403,
        'info'=>'参数 Query[url_id] 缺失/错误'
    ];
    // 输出数据
    $ApiFunction->logs('service_chain_select','短链查询',0,'Query_UrlIdFail');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);