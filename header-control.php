<?PHP
/*
 * XF_TLS 前置载入配置
 * 未开源
 */

/** @var TYPE_NAME $SqlConn */
/** @var TYPE_NAME $setting */

// 设置请求头
header('Content-Type: application/json;charset=utf-8');
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With,X-PINGOTHER,Content-Type');

// 获取数据（获取数据库信息）
include($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');
include($_SERVER['DOCUMENT_ROOT'].'/plugins/sql_conn.php');