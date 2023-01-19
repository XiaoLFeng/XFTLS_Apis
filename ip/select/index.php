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
include $_SERVER['DOCUMENT_ROOT'] . '/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'] . '/modules/ApiFunction.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/modules/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/modules/calc_for_ipv6.php';
$ApiFunction = new ApiFunction();
$User = new User();
$calc = new calc_for_ipv6();

// 获取参数
// GET
$GetData = array(
    'ukey' => urldecode(htmlspecialchars($_GET['ukey'])),
    'type' => urldecode(htmlspecialchars($_GET['type'])),
    'ip' => urldecode(htmlspecialchars($_GET['ip'])),
);

// 函数构建
if ($ApiFunction->Get_ukey($GetData['ukey'])) {
    // 判断类型
    if ($GetData['type'] == 'ipv4') {
        // 正则判断
        if (preg_match('/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/',$GetData['ip'])) {
            // 函数计算
            $ip = explode('.',$GetData['ip']);
            $ip_number = 16777216*$ip[0] + 65536*$ip[1] + 256*$ip[2] + $ip[3];

            // 数据库筛查
            $Result_ip = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['ipv4']." WHERE `start_ip`<= ".$ip_number." AND `end_ip`>= ".$ip_number);
            $Result_ip_Object = mysqli_fetch_object($Result_ip);

            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'查询成功',
                'data'=>array(
                  'ip'=>$GetData['ip'],
                  'country_sx'=>$Result_ip_Object->country_sx,
                  'country'=>$Result_ip_Object->country,
                  'province'=>$Result_ip_Object->province,
                  'city'=>$Result_ip_Object->city,
                  'longitude'=>$Result_ip_Object->longitude,
                  'latitude'=>$Result_ip_Object->latitude,
                  'post_code'=>$Result_ip_Object->post_code,
                  'time_zone'=>$Result_ip_Object->time_zone,
                ),
            );
            // 输出数据
            $ApiFunction->logs('ip_search','IP查询',1);
        } else {
            // 编译数据
            $data = array(
                'output'=>'IP_FALSE',
                'code'=>403,
                'info'=>'参数 Query[ip] 格式错误'
            );
            // 输出数据
            $ApiFunction->logs('ip_search','格式不符',0,'Query_ip');
            header("HTTP/1.1 403 Forbidden");
        }
    } elseif ($GetData['type'] == 'ipv6') {
        // 正则判断
        if (preg_match('/^([\da-fA-F]{1,4}:){7}[\da-fA-F]{1,4}$/',$GetData['ip'])) {
            // 函数计算
            $ip = explode(':',$GetData['ip']);

            // 数据库筛查
            $Result_ip = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['ipv6']." WHERE `start_ip` <= ".$calc->calc_ipv6($ip)." AND `end_ip` >= ".$calc->calc_ipv6($ip));
            $Result_ip_Object = mysqli_fetch_object($Result_ip);

            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'查询成功',
                'data'=>array(
                    'ip'=>$calc->calc_ipv6($ip),
                    'country_sx'=>$Result_ip_Object->country_sx,
                    'country'=>$Result_ip_Object->country,
                    'province'=>$Result_ip_Object->province,
                    'city'=>$Result_ip_Object->city,
                    'longitude'=>$Result_ip_Object->longitude,
                    'latitude'=>$Result_ip_Object->latitude,
                    'post_code'=>$Result_ip_Object->post_code,
                    'time_zone'=>$Result_ip_Object->time_zone,
                ),
            );
            // 输出数据
            $ApiFunction->logs('ip_search','IP查询',1);
        } else {
            // 编译数据
            $data = array(
                'output'=>'IP_FALSE',
                'code'=>403,
                'info'=>'参数 Query[ip] 格式错误'
            );
            // 输出数据
            $ApiFunction->logs('ip_search','格式不符',0,'Query_ip');
            header("HTTP/1.1 403 Forbidden");
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'TYPE_ERROR',
            'code'=>403,
            'info'=>'参数 Query[type] 缺失/错误'
        );
        // 输出数据
        $ApiFunction->logs('ip_search','参数不符',0,'Query_type');
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
    $ApiFunction->logs('ip_search','密钥检查',0,'Query_ukey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);