<?php

class Data_Check
{
    /**
     * 创建随机图库
     * @param string $name 图库名字
     * @param string $open 是否开放图库
     * @return string 返回不符合类型的错误代码，若判断通过为 string 类型的 TRUE
     */
    public function Service_Acgurl_Create(string $name, string $open): string
    {
        if (preg_match("/^[一-龥A-Za-z0-9]{2,40}$/",$name)) {
            if (preg_match('/^[0-1]$/',$open)) return 'TRUE';
            else return 'OPEN_FALSE';
        } else return 'NAME_FALSE';
    }

    /**
     * 删除随机图库
     * @param string $key 图库序列号
     * @return bool 检查正确返回TRUE，否则为FALSE
     */
    public function Service_Acgurl_Delete(string $key): bool
    {
        if (preg_match("/^[XFACG]{5}[0-9]{10}[A-Za-z0-9]{5}$/",$key)) return true;
        else return false;
    }

    /**
     * 编辑随机图库
     * @param string $key 图库序列号
     * @param string $name 图库名字
     * @param string $url 图库内随机图片URL地址
     * @param $open
     * @return string 返回不符合类型的错误代码，若判断通过为 string 类型的 TRUE
     */
    public function Service_Acgurl_Edit(string $key, string $name, string $url, $open): string
    {
        if (preg_match("/^[XFACG]{5}[0-9]{10}[A-Za-z0-9]{5}$/",$key)) {
            if (preg_match("/^[一-龥A-Za-z0-9]{2,40}$/",$name)) {
                if (empty($url) or preg_match("/[^;']+/",$url)) {
                    if (preg_match("/^[0-1]$/",$open)) return 'TRUE';
                    else return 'OPEN_FALSE';
                } else return 'URL_FALSE';
            } else return 'NAME_FALSE';
        } else return 'KEY_FALSE';
    }

    /**
     * 创建网站分析SID
     * @param string $name 网站分析名字
     * @param string $domain 需要统计的域名
     * @param int $open 是否开放数据收集
     * @return string 返回不符合类型的错误代码，若判断通过为 string 类型的 TRUE
     */
    public function Service_Analytics_Create(string $name, string $domain, int $open): string {
        if (preg_match('/^[一-龥A-Za-z0-9]{2,40}$/',$name)) {
            if (preg_match("/[^;']+/",$domain)) {
                if (preg_match('/^[0-1]$/',$open)) return "TRUE";
                else return "OPEN_FALSE";
            } else return "DOMAIN_FALSE";
        } else return "NAME_FALSE";
    }
}