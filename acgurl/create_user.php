<?php
/**
 * @ignore XF_TLS 项目组 API 部
 * @ignore 全部代码未开源
 * @internal 只允许SSID模式下使用
 */

// 载入头
include $_SERVER['DOCUMENT_ROOT'].'/header-control.php';

// 载入class
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/ApiFunction.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/User.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Mailer.php';
$ApiFunction = new ApiFunction();
$User = new User();
$Mail = new SendMail();

// 获取参数
// GET
$GetData = array(
    'ssid'=>urldecode(htmlspecialchars($_GET['ssid'])),
    ''
);