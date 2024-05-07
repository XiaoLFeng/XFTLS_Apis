<?PHP 
/*
 * 筱锋xiao_lfeng
 * 数据库链接系统（插件）
 */

//定义参数
/**
 * @var string $setting
 */
$sql_host = $setting['SQL']['host'];
$sql_dbname = $setting['SQL']['dbname'];
$sql_username = $setting['SQL']['username'];
$sql_password = $setting['SQL']['password'];
//判断数据库端口
if($setting['sql']['port'] == 3306 or $setting['sql']['port'] == NULL){
    //定义参数
    $sql_port = 3306;
} else {
    //定义参数
    $sql_port = $setting['sql']['port'];
}
//连接数据库
$SqlConn = mysqli_connect($sql_host,$sql_username,$sql_password,$sql_dbname,$sql_port);
/*
//检查链接
if($sql_conn->connect_error){
	die('数据库连接失败！'.$conn->connect_error);
}
*/