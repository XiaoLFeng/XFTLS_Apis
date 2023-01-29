<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

/**
 * @var string $SqlConn
 * @var string $setting
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
$ApiFunction = new ApiFunction();

// 获取参数
// GET
$GetData = array(
    'ukey'=>urldecode(htmlspecialchars($_GET['ukey'])),
    'key'=>urldecode(htmlspecialchars($_GET['key'])),
);

if ($ApiFunction->Get_ukey($GetData['ukey'])) {
    $Result_User = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['user']." WHERE `ukey`='".$GetData['ukey']."'");
    $Result_User_Object = mysqli_fetch_object($Result_User);

    if (empty($GetData['key'])) {
        for ($i=0; $i <= 6; $i++) {
            $array_result[$i]['date'] = date('Y-m-d',strtotime("-".$i." day"));
            $Result_AcgurlLog = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl_log']." WHERE `uid`='".$Result_User_Object->id."' AND `date` LIKE '%".date('Y-m-d',strtotime("-".$i." days"))."%'");
            $Result_AcgurlLog_Row = mysqli_num_rows($Result_AcgurlLog);
            $array_result[$i]['today_total'] = $Result_AcgurlLog_Row;
            $Result_AcgurlLog = null;
            $Result_AcgurlLog_Row = null;
            for ($j=0; $j<=23; $j++) {
                $Result_AcgurlLog = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl_log']." WHERE `uid`=".$Result_User_Object->id." AND `date` LIKE '%".date('Y-m-d H',strtotime(date('Y-m-d',strtotime('-'.$i.' days'))." 00:00:00")+($j*3600))."%'");
                $Result_AcgurlLog_Row = mysqli_num_rows($Result_AcgurlLog);
                $array_result[$i]['data'][$j] = $Result_AcgurlLog_Row;
            }
            $Result_AcgurlLog = null;
            $Result_AcgurlLog_Row = null;
        }
        // 计算最多的IP
        for ($i=0; $i<=6; $i++) {
            $Result_AcgurlLog = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl_log']." WHERE `uid`='".$Result_User_Object->id."' AND `date` LIKE '%".date('Y-m-d',strtotime("-".$i." days"))."%'");
            while ($Result_AcgurlLog_Object = mysqli_fetch_object($Result_AcgurlLog)) {
                if (empty($array_info['ip'][$Result_AcgurlLog_Object->ip])) {
                    $array_info['ip'][$Result_AcgurlLog_Object->ip] = 1;
                } else {
                    $array_info['ip'][$Result_AcgurlLog_Object->ip] ++;
                }

            }
            $Result_AcgurlLog = null;
        }

        // 计算最多的URL
        for ($i=0; $i<=6; $i++) {
            $Result_AcgurlLog = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl_log']." WHERE `uid`='".$Result_User_Object->id."' AND `date` LIKE '%".date('Y-m-d',strtotime("-".$i." days"))."%'");
            while ($Result_AcgurlLog_Object = mysqli_fetch_object($Result_AcgurlLog)) {
                if (empty($array_info['url'][$Result_AcgurlLog_Object->url])) {
                    $array_info['url'][$Result_AcgurlLog_Object->url] = 1;
                } else {
                    $array_info['url'][$Result_AcgurlLog_Object->url] ++;
                }

            }
            $Result_AcgurlLog = null;
        }

        // 数据整理
        arsort($array_info['ip']);
        arsort($array_info['url']);

        // 编译数据
        $data = array(
            'output'=>'SUCCESS',
            'code'=>200,
            'info'=>'调用成功',
            'data'=>[
                'info'=>$array_info,
                'result'=>$array_result,
            ],
        );
        // 输出数据
        $ApiFunction->logs('service_acgurl_logs','调用记录',1);
    } else {
        if (preg_match("/^[XFACG]{5}[0-9]{10}[A-Za-z0-9]{5}$/",$GetData['key'])) {
            $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE `access_key`='".$GetData['key']."'");
            $Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl);

            if ($Result_Acgurl_Object->uid == $Result_User_Object->id) {
                for ($i=0; $i <= 6; $i++) {
                    $array_result[$i]['date'] = date('Y-m-d',strtotime("-".$i." day"));
                    $Result_AcgurlLog = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl_log']." WHERE `uid`='".$Result_User_Object->id."' AND `date` LIKE '%".date('Y-m-d',strtotime("-".$i." days"))."%' AND `key`='".$GetData['key']."'");
                    $Result_AcgurlLog_Row = mysqli_num_rows($Result_AcgurlLog);
                    $array_result[$i]['today_total'] = $Result_AcgurlLog_Row;
                    $Result_AcgurlLog = null;
                    $Result_AcgurlLog_Row = null;
                    for ($j=0; $j<=23; $j++) {
                        $Result_AcgurlLog = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl_log']." WHERE `uid`=".$Result_User_Object->id." AND `date` LIKE '%".date('Y-m-d H',strtotime(date('Y-m-d',strtotime('-'.$i.' days'))." 00:00:00")+($j*3600))."%' AND `key`='".$GetData['key']."'");
                        $Result_AcgurlLog_Row = mysqli_num_rows($Result_AcgurlLog);
                        $array_result[$i]['data'][$j] = $Result_AcgurlLog_Row;
                    }
                }
                // 编译数据
                $data = array(
                    'output'=>'SUCCESS',
                    'code'=>200,
                    'info'=>'调用成功',
                    'data'=>$array_result,
                );
                // 输出数据
                $ApiFunction->logs('service_acgurl_logs','调用记录',1);
            } else {
                // 编译数据
                $data = array(
                    'output'=>'IMAGE_NOT_YOUR',
                    'code'=>403,
                    'info'=>'这个图库不是你的'
                );
                // 输出数据
                $ApiFunction->logs('service_acgurl_logs','调用记录',0,'Query_key[Image_Not_Your]');
                header("HTTP/1.1 403 Forbidden");
            }
        } else {
            // 编译数据
            $data = array(
                'output'=>'KEY_FALSE',
                'code'=>403,
                'info'=>'参数 Query[key] 错误，不正确的Key'
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl_logs','调用记录',0,'Query_key[KEY_FALSE]');
            header("HTTP/1.1 403 Forbidden");
        }
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'UEKY_DENY',
        'code'=>403,
        'info'=>'参数 Query[ukey] 缺失/错误'
    );
    // 输出数据
    $ApiFunction->logs('service_acgurl_logs','密钥调用',0,'Query_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);