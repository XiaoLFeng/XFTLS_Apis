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

/**
 * @var int $Cache_Data 循环取得单数缓存
 * @var int $Token_RandData Token最终取得值
 * @var int $Token_IpDistinguish Token取得IP区分码
 * @var string $Result_Token_Object->Token 数据库获取Token
 * @var array $Token Token进行拆分后组成数组
 * @var int $Token_IpDistinguish Token取得IP区分码
 * @var array $Array_IpAddress 获取抛开符号的IP地址
 * @var array $Array_PutIn 补充数组
 * @var int $i 循环位数
 * @var int $j 循环位数
 * @var string $Token_IP Token取得IP
 * @var array $ApiIP 从IP库获取信息
 * @var int $Token_Place Token区域码
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
$ApiFunction = new ApiFunction();

// 获取参数
// GET
$GetData = array(
    'okey'=>urldecode(htmlspecialchars($_GET['okey'])),
);

// 逻辑运算
if ($ApiFunction->Get_okey($GetData['okey'])) {
    // 算法构建
    for ($i=0; $i<=1; $i++) {
        $Cache_Data = rand(1,7);
        $Token_RandData = $Token_RandData.$Cache_Data;
    }

    if (preg_match('/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}/',$_SERVER["REMOTE_ADDR"])) {
        $Token_IpDistinguish = 0;
    } else {
        $Token_IpDistinguish = 1;
    }

    if (!$Token_IpDistinguish) {
        $Array_IpAddress = explode('.',$_SERVER["REMOTE_ADDR"]);
        for ($i=0; $i<=3; $i++) {
            if (strlen((int)$Array_IpAddress[$i]) != 3) $Array_IpAddress[$i] = Repair0(3-strlen((int)$Array_IpAddress[$i])).$Array_IpAddress[$i];
        }
    } else {
        $Array_IpAddress = explode(':',$_SERVER["REMOTE_ADDR"]);
        for ($i=0; $i<=7; $i++) {
            if (empty($Array_IpAddress[$i])) {
                if (!empty($Array_IpAddress[$i+1])) continue; else break;
            } else {
                if (strlen($Array_IpAddress[$i]) < 4) $Array_IpAddress[$i] = Repair0(4-strlen($Array_IpAddress[$i])).$Array_IpAddress[$i];
            }
        }
        // 补充位数
        for ($i=0; $i<=count($Array_IpAddress)-1; $i++) {
            if (in_array(null,$Array_IpAddress)) {
                for ($j=0; $j<=7-count($Array_IpAddress); $j++) {
                    $Array_PutIn[$j] = 0000;
                }
                for ($j=0; $j<=7-count($Array_IpAddress); $j++) {
                    if ($Array_IpAddress[$j] == null) break;
                }
                array_splice($Array_IpAddress,($j+1),0,$Array_PutIn);
            }
        }
    }
    $Token_IP = implode(null,$Array_IpAddress);

    // 获取IP等参数信息
    $ApiIP_url = 'https://api.x-lf.cn/ip/select/?ukey=XFUKEY30531674056177fhvgb2tyar&type=ipv4&ip=116.25.145.188';
    $ApiIP_ch = curl_init($ApiIP_url);
    curl_setopt($ApiIP_ch,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ApiIP_ch, CURLOPT_RETURNTRANSFER, true);
    $ApiIP = curl_exec($ApiIP_ch);
    $ApiIP = json_decode($ApiIP,true);

    if (strlen(intval($ApiIP['data']['longitude'])) != 3) {
        $ApiIP['data']['longitude'] = Repair0(3-strlen(intval($ApiIP['data']['longitude']))).$ApiIP['data']['longitude'];
    }
    if (strlen(intval($ApiIP['data']['latitude'])) != 3) {
        $ApiIP['data']['latitude'] = Repair0(3-strlen(intval($ApiIP['data']['latitude']))).$ApiIP['data']['latitude'];
    }

    $Token_Place = Repair0(6-strlen(intval($ApiIP['data']['longitude']).intval($ApiIP['data']['latitude']))).intval($ApiIP['data']['longitude']).intval($ApiIP['data']['latitude']);

    // 逻辑计算
    if ($Token_IpDistinguish) {
        // 匹配ipv6下全部符合0-9的字符并组合后强制转译成整形变量
        preg_match_all('/[0-9]+/',$Token_IP,$Cache_IP);
        $Token_IPs = substr(implode(null,$Cache_IP[0]),0,12);
    }

    $Token_Check = round($Token_IPs / $Token_Place + $Token_RandData) % 8;
    $Token_Calc = substr(round($Token_IPs / $Token_Place + $Token_RandData),-3,3);
    $Token = $Token_RandData.$Token_IpDistinguish.$Token_IP.$Token_Place.$Token_Check.$Token_Calc;
    // 编译数据
    $data = array(
        'output'=>'SUCCESS',
        'code'=>200,
        'info'=>'Token已生成',
        'data'=>[
            'token'=>$Token,
            'hash_token'=>password_hash($Token,PASSWORD_DEFAULT)
        ]
    );
    // 输出数据
    $ApiFunction->logs('opensource_token_create','生成Token',1);

} else {
    // 编译数据
    $data = array(
        'output'=>'OEKY_DENY',
        'code'=>403,
        'info'=>'参数 Post[okey] 缺失/错误',
    );
    // 输出数据
    $ApiFunction->logs('opensource_token_create','密钥检查',0,'Query_okey');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);

/**
 * @param $long
 * @return int
 */
function Repair0 ($long)
{
    /** @var int $data */
    for ($i=1; $i<=$long; $i++) $data = $data.(0);
    return $data;
}