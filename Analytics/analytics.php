<?php
/*
 * XF_TLS 项目组 API 部
 * 全部代码未开源
 */

// 引入配置
require_once $_SERVER['DOCUMENT_ROOT'].'/modules/Key.php';
$Key = new Key();

// 定义表头
header( 'Content-Type:text/html;charset=utf-8');

// 判断内容是否赋予COOKIE
if (empty($_COOKIE['XFAY_User'])) {
    header('Set-Cookie: XFAY_User='.$Key->service_analytics_user_create(4).'; SameSite=None; Secure; Expires='.date('Y-m-d H:i:s',time()+86400).'; Domain=.api.x-lf.cn; Path=/');
} else {
    header('Set-Cookie: XFAY_User='.$_COOKIE['XFAY_User'].'; SameSite=None; Secure; Expires='.date('Y-m-d H:i:s',time()+86400).'; Domain=.api.x-lf.cn; Path=/');
}
?>
<!doctype html>
<html>
    <script src="https://api.x-lf.cn/Analytics/getIP.js.php" type="module"></script>
    <script src="https://api.x-lf.cn/sources/js/jquery.min.js" type="module"></script>
    <script>
        $.ajax({
            async: true,
            type: "GET",
            data: {
                "code":"<?php echo $_COOKIE['XFAY_User']?>",
                "domain":"<?php echo $_SERVER['SERVER_NAME'] ?>",
                "referer":"<?php echo $_SERVER['HTTP_REFERER'] ?>",
                "UA":"<?php echo $_SERVER['HTTP_USER_AGENT'] ?>",
                "IP":"<?php echo $_SERVER['REMOTE_ADDR'] ?>"
            },
            url: "/Analytics/index.php"
        });
    </script>
</html>