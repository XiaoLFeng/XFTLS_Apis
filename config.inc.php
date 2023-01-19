<?PHP
/*
 * 配置文件
 * 
 * 默认情况，请勿修改
 * 敏感数据，请勿外传
 * 
 * 作者：筱锋xiao_lfeng
 * 最终所有权归 筱锋xiao_lfeng 所有
 */

// 格式化数据
$setting = array();

/******************* 开放管理 *******************/
// 站点启用
$setting['WEB']['Start'] = TRUE;
// 站点开启 Debug 模式
$setting['WEB']['DeBUG'] = FALSE;
// 站点通信密钥
$setting['SSID'] = "20040227";
// 验证码有效时间
$setting['CAPTCHA_TIME'] = 900;

/******************** 数据库 ********************/
// 数据库地址
$setting['SQL']['host'] = '127.0.0.1';
// 数据库名字
$setting['SQL']['dbname'] = 'xf_tls';
// 数据库用户名
$setting['SQL']['username'] = 'root';
// 数据库密码
$setting['SQL']['password'] = 'YNily20040227';

/******************** 数据表 ********************/
// TLS基本数据表
$setting['TABLE']['info'] = 'tls_info';
// TLS日志数据表
$setting['TABLE']['logs'] = 'tls_logs';
// TLS用户数据表
$setting['TABLE']['user'] = 'tls_user';
// TLS头像数据表
$setting['TABLE']['avatar'] = 'tls_avatar';
// TLS随机图库数据表
$setting['TABLE']['acgurl'] = 'tls_acgurl';
// TLS用户高级SSID数据表
$setting['TABLE']['user_ssid'] = 'tls_ssid';
// TLS验证码数据表
$setting['TABLE']['captcha'] = 'tls_captcha';
// TLS_IPv4数据表
$setting['TABLE']['ipv4'] = 'tls_ipv4_list';
// TLS_IPv6数据表
$setting['TABLE']['ipv6'] = 'tls_ipv6_list';

/******************* 邮箱管理 *******************/
// 邮箱地址
$setting['SMTP']['HOST'] = 'smtp.qiye.aliyun.com';
// 是否允许SMTP认证
$setting['SMTP']['AUTH'] = true;
// 邮箱用户名（邮箱）
$setting['SMTP']['USER'] = 'noreplay@x-lf.cn';
// 邮箱密码或授权码
$setting['SMTP']['PASSWORD'] = 'X+7ily20040722';
// 发件人
$setting['SMTP']['NAME'] = 'noreplay@x-lf.cn';
// 展示名字
$setting['SMTP']['DISPLAY'] = 'noreplay@x-lf.cn';
// 收件名字
$setting['SMTP']['REPLAY'] = 'gm@x-lf.cn';
// 非SSL端口
$setting['SMTP']['NOSSL'] = 25;
// SSL端口
$setting['SMTP']['SSL'] = 465;