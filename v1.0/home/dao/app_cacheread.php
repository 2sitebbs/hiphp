<?php
/*
 * cache read dao implement
 * by lane @2014-04-19
 * ----------------------------
 * support memcache and redis
*/
require_once 'app_readapi.php';

class AppCacheRead extends CacheRead {
    
    //------------------自定义函数-----------------



    //------------------示例函数-------------------
    //纯使用redis的函数
    function savePaiming($username, $num, $expire = 7776000) {
        if ($this->cache->type() != 'redis') {
            return null;
        }

        $key = 'paiming';
        return $this->cache->zAdd($key, $num, $username, $expire);
    }

    function getPaiming($startIndex, $endIndex = -1) {
        if ($this->cache->type() != 'redis') {
            return null;
        }

        $key = 'paiming';
        return $this->cache->zRevRange($key, $startIndex, $endIndex);
    }

}
