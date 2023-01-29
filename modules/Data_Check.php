<?php

class Data_Check
{
    /**
     * @param string $name
     * @param string $open
     * @return string
     */
    public function Service_Acgurl_Create(string $name, string $open): string
    {
        if (preg_match("/^[\一-\龥A-Za-z0-9]{2,40}$/",$name)) {
            if (preg_match('/^[0-1]$/',$open)) {
                return 'TRUE';
            } else {
                return 'OPEN_FALSE';
            }
        } else {
            return 'NAME_FALSE';
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function Service_Acgurl_Delete(string $key): bool
    {
        if (preg_match("/^[XFACG]{5}[0-9]{10}[A-Za-z0-9]{5}$/",$key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $key
     * @param string $name
     * @param string $url
     * @param $open
     * @return string
     */
    public function Service_Acgurl_Edit(string $key, string $name, string $url, $open): string
    {
        if (preg_match("/^[XFACG]{5}[0-9]{10}[A-Za-z0-9]{5}$/",$key)) {
            if (preg_match("/^[\一-\龥A-Za-z0-9]{2,40}$/",$name)) {
                if (empty($url) or preg_match("/[^;']+/",$url)) {
                    if (preg_match("/^[0-1]$/",$open)) {
                        return 'TRUE';
                    } else {
                        return 'OPEN_FALSE';
                    }
                } else {
                    return 'URL_FALSE';
                }
            } else {
                return 'NAME_FALSE';
            }
        } else {
            return 'KEY_FALSE';
        }
    }
}