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
$ApiFunction = new ApiFunction();

// 获取参数
// GET
$GetData = array(
    'key'=>urldecode(htmlspecialchars($_GET['key'])),
    'type'=>urldecode(htmlspecialchars($_GET['type'])),
);

// 函数构建
function OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting) {
    if ($Result_Acgurl_Object->open == 1) {
        if ($GetData['type'] == 'json') {
            $url_data = explode(PHP_EOL,$Result_Acgurl_Object->url);
            // 编译数据
            $data = array(
                'output'=>'SUCCESS',
                'code'=>200,
                'info'=>'数据输出完毕',
                'data'=>[
                    'url'=>str_replace(array("\r\n", "\r", "\n"),'',$url_data[array_rand($url_data)]),
                ],
            );
            // 输出数据
            $ApiFunction->logs('service_acgurl','随机图库_Json',1);
            $url_log = $_SERVER['HTTP_REFERER'];
            mysqli_query($SqlConn,"INSERT INTO ".$setting['TABLE']['Service']['Acgurl_log']." (`key`,`data`,`ip`,`uid`,`date`,`url`) VALUES ('".$GetData['key']."','调用成功','".$_SERVER["REMOTE_ADDR"]."','".$Result_Acgurl_Object->uid."','".date('Y-m-d H:i:s')."','".$url_log."')");
            return $data;
        } else {
            header("Content-Type: image/jpeg;text/html; charset=utf-8");
            $url_data = explode(PHP_EOL,$Result_Acgurl_Object->url);
            $Image_Url = file_get_contents(str_replace(array("\r\n", "\r", "\n"),'',$url_data[array_rand($url_data)]),true);
            echo $Image_Url;
            // 输出数据
            $ApiFunction->logs('service_acgurl','随机图库_Url',1);
            $url_log = $_SERVER['HTTP_REFERER'];
            mysqli_query($SqlConn,"INSERT INTO ".$setting['TABLE']['Service']['Acgurl_log']." (`key`,`data`,`ip`,`uid`,`date`,`url`) VALUES ('".$GetData['key']."','调用成功','".$_SERVER["REMOTE_ADDR"]."','".$Result_Acgurl_Object->uid."','".date('Y-m-d H:i:s')."','".$url_log."')");
            exit();
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'CLOSED',
            'code'=>200,
            'info'=>'图库已关闭',
        );
        // 输出数据
        $ApiFunction->logs('service_acgurl','图库关闭',1);
        mysqli_query($SqlConn,"INSERT INTO ".$setting['TABLE']['Service']['Acgurl_log']." (`key`,`data`,`ip`,`uid`,`date`) VALUES ('".$GetData['key']."','图库关闭','".$_SERVER["REMOTE_ADDR"]."','".$Result_Acgurl_Object->uid."','".date('Y-m-d H:i:s')."')");
        return $data;
    }
}

// 逻辑运算
if (!empty($GetData['key'])) {
    // 图库密钥检索
    $Result_Acgurl = mysqli_query($SqlConn,"SELECT * FROM ".$setting['TABLE']['Service']['Acgurl']." WHERE access_key='".$GetData['key']."'");
    $Result_Acgurl_Object = mysqli_fetch_object($Result_Acgurl);

    // 查询是否有此图
    if ($Result_Acgurl_Object->id != null) {
        // 检查黑名单
        if (!$Result_Acgurl_Object->blacklist_open) {
            if ($Result_Acgurl_Object->domain_open == 0) {
                $data = OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting);
            } elseif ($Result_Acgurl_Object->domain_open == 1) {
                // 如果为 1 为 domain 白名单
                // 从 Referer 数据中查询数据信息
                preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/',$_SERVER['HTTP_REFERER'],$UserReferer);
                if (in_array($UserReferer[0],explode(';',$Result_Acgurl_Object->domain))) {
                    $data = OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting);
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'DOMAIN_BAN_WHITELIST',
                        'code'=>403,
                        'info'=>'您不在白名单内',
                    );
                    // 输出数据
                    $ApiFunction->logs('service_acgurl','Domain白名单',0,'Server_DomainBan');
                    header("HTTP/1.1 403 Forbidden");
                }
            } elseif ($Result_Acgurl_Object->domain_open == 2) {
                // 如果为 2 为 domain 黑名单
                preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/',$_SERVER['HTTP_REFERER'],$UserReferer);
                if (!in_array($UserReferer[0],explode(';',$Result_Acgurl_Object->domain))) {
                    $data = OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting);
                } else {
                    // 编译数据
                    $data = array(
                        'output'=>'DOMAIN_BAN_BLACKLIST',
                        'code'=>403,
                        'info'=>'此Domain为管理员设定的黑名单Domain',
                    );
                    // 输出数据
                    $ApiFunction->logs('service_acgurl','Domain黑名单',0,'Server_DomainBan');
                    header("HTTP/1.1 403 Forbidden");
                }
            } else {
                // 如果不是这些数字则重置为 0 并且清空名单中的数据
                mysqli_query($SqlConn,"UPDATE ".$setting['TABLE']['Service']['Acgurl']." SET `domain_open`=0 WHERE `access_key`='".$GetData['key']."'");
                header('location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            }
        } else {
            // 处理数据
            if (!in_array($_SERVER["REMOTE_ADDR"],explode(';',$Result_Acgurl_Object->blacklist))) {
                // 数据处理
                if ($Result_Acgurl_Object->domain_open == 0) {
                    $data = OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting);
                } elseif ($Result_Acgurl_Object->domain_open == 1) {
                    // 如果为 1 为 domain 白名单
                    // 从 Referer 数据中查询数据信息
                    preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/',$_SERVER['HTTP_REFERER'],$UserReferer);
                    if (in_array($UserReferer[0],explode(';',$Result_Acgurl_Object->domain))) {
                        $data = OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'DOMAIN_BAN_WHITELIST',
                            'code'=>403,
                            'info'=>'您不在白名单内',
                        );
                        // 输出数据
                        $ApiFunction->logs('service_acgurl','Domain白名单',0,'Server_DomainBan');
                        header("HTTP/1.1 403 Forbidden");
                    }
                } elseif ($Result_Acgurl_Object->domain_open == 2) {
                    // 如果为 2 为 domain 黑名单
                    preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/',$_SERVER['HTTP_REFERER'],$UserReferer);
                    if (!in_array($UserReferer[0],explode(';',$Result_Acgurl_Object->domain))) {
                        $data = OpenToUse($Result_Acgurl_Object, $GetData, $ApiFunction, $SqlConn, $setting);
                    } else {
                        // 编译数据
                        $data = array(
                            'output'=>'DOMAIN_BAN_BLACKLIST',
                            'code'=>403,
                            'info'=>'此Domain为管理员设定的黑名单Domain',
                        );
                        // 输出数据
                        $ApiFunction->logs('service_acgurl','Domain黑名单',0,'Server_DomainBan');
                        header("HTTP/1.1 403 Forbidden");
                    }
                } else {
                    // 如果不是这些数字则重置为 0 并且清空名单中的数据
                    mysqli_query($SqlConn,"UPDATE ".$setting['TABLE']['Service']['Acgurl']." SET `domain_open`=0 WHERE `access_key`='".$GetData['key']."'");
                    header('location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                }
            } else {
                // 编译数据
                $data = array(
                    'output'=>'IP_BAN',
                    'code'=>403,
                    'info'=>'此IP为管理员设定的黑名单IP',
                );
                // 输出数据
                $ApiFunction->logs('service_acgurl','IP黑名单',0,'Server_IpBan');
                header("HTTP/1.1 403 Forbidden");
            }
        }
    } else {
        // 编译数据
        $data = array(
            'output'=>'KEY_DENY',
            'code'=>403,
            'info'=>'参数 Query[key] 错误，没有这个图库序列',
        );
        // 输出数据
        $ApiFunction->logs('service_acgurl','序列查找',0,'Query_key');
        header("HTTP/1.1 403 Forbidden");
    }
} else {
    // 编译数据
    $data = array(
        'output'=>'KEY_NONE',
        'code'=>403,
        'info'=>'参数 Query[key] 缺失',
    );
    // 输出数据
    $ApiFunction->logs('service_acgurl','序列查找',0,'Query_key');
    header("HTTP/1.1 403 Forbidden");
}
echo json_encode($data,JSON_UNESCAPED_UNICODE);