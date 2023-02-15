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
// GET
$GetPost = [
    'domain'=>urldecode(htmlspecialchars($_GET['domain'])),
    'referer'=>urldecode(htmlspecialchars($_GET['referer'])),
    'UA'=>urldecode(htmlspecialchars($_GET['UA'])),
    'IP'=>urldecode(htmlspecialchars($_GET['IP'])),
];

// 算法构建
if () {

}