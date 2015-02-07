<?php
/*
 * cache read dao implement
 * by lane @2014-04-19
 * ----------------------------
 * support memcache and redis
*/
require_once 'mongo_app_readapi.php';

class MongoAppCacheRead extends MongoDAOReadApis {
    protected static $impl;
    private $cache;
    private $cacheTime;

    //AppReadApis实例
    private static $appReadApi;

    /**
    * 动态函数调用
    * 不支持永久缓存，如果缓存时间为0，则不缓存，直接从数据库取数据
    */
    /*{{{*/
    function __call($method, $arguments) {
        $key = md5("{$method}_" . md5(serialize($arguments)));   //函数名 + 参数md5作key
        
        if ($this->cacheTime == 0) {    //不取缓存
            $this->cache->set($key, '', 1);        //try to clean
            
            if (!method_exists(self::$appReadApi, $method)) {
                return self::$appReadApi->__call($method, $arguments);
            }else {
                return call_user_func_array(array(self::$appReadApi, $method), $arguments);
            }
        }
        
        $arr = $this->cache->get($key);
        if (!empty($arr)) {
            return $arr;
        }

        //调用AppReadApis类中取数据的方法
        if (!method_exists(self::$appReadApi, $method)) {
            $arr = self::$appReadApi->__call($method, $arguments);
        }else {
            $arr = call_user_func_array(array(self::$appReadApi, $method), $arguments);
        }
        $this->cache->set($key, $arr, $this->cacheTime);
        return $arr;
    }
    /*}}}*/

    //implement the abstract function
    /*{{{*/
    public static function getImplement($driver, $tablepre, $class = null) {
        if (empty(self::$impl)) {
            $class = __CLASS__;
            self::$impl = new $class($driver, $tablepre);
            self::$appReadApi = MongoAppReadApis::getImplement($driver, $tablepre);
        }
        return self::$impl;
    }
    /*}}}*/

    //重定义表明前缀和数据库切换方法
    public function setTablePre($tablepre) {
        $this->tablepre = $tablepre;
        self::$appReadApi->setTablePre($tablepre);
    }

    //重定义表明前缀和数据库切换方法
    public function setDatabase($dbname) {
        return $this->wrapper->select_db($dbname);
        self::$appReadApi->setDatabase($tablepre);
    }

    public function setCacheTime($cacheTime = 1800) {
        $this->cacheTime = $cacheTime;
    }

    public function setCacheHandler($memcache, $cacheTime = 1800) {
        $this->cacheTime = $cacheTime;
        $this->cache = $memcache;
    }




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
