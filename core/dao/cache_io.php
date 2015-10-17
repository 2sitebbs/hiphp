<?php
/*
 * cache io class
 * by lane @2015-04-17
 * ----------------------------
 * 需要依赖AppReadApis类和MongoAppReadApis类
*/
class CacheRead {
    protected static $impl;
    protected $cache;
    protected $cacheTime = 1800;

    //AppReadApis实例
    protected static $appReadApi;

    /**
    * 动态函数调用
    * 不支持永久缓存，如果缓存时间为0，则不缓存，直接从数据库取数据
    */
    /*{{{*/
    function __call($method, $arguments) {
        $key = $this->getCacheKey($method, $arguments);   //函数名 + 参数md5作key

        //如果没有缓存key或缓存时间为0，则不从缓存取数据
        if (empty($key) || $this->cacheTime == 0) {
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
    public static function getImplement($driver, $tablepre, $appCacheClass = null, $appDaoClass = 'AppReadApis') {
        $cacheClass = $appCacheClass ? $appCacheClass : __CLASS__;
        $key = "{$cacheClass}_{$appDaoClass}";
        if (!isset(self::$impl[$key]) || empty(self::$impl[$key])) {
            self::$impl[$key] = new $cacheClass($driver, $tablepre);
            self::$appReadApi = $appDaoClass::getImplement($driver, $tablepre);
        }
        return self::$impl[$key];
    }
    /*}}}*/

    //解析方法名，得到操作方法、表名和字段名
    protected function parseMethod($method) {
        $reg = '/^(get|add|update|delete)(\w+)(?:By(\w+))?$/iU';
        preg_match($reg, $method, $match);

        $out = array();
        $out['method'] = @$match[1];            //ocphp支持的方法或者返回空

        //如果为自定义的方法，则使用方法名做缓存key
        $out['table'] = isset($match[2]) ? strtolower($match[2]) : strtolower($method);

        $out['fields'] = @$match[3];

        return $out;
    }

    /**
     * 获得缓存key
     **/
    protected function getCacheKey($method, $arguments) {
        $out = $this->parseMethod($method);

        $callCacheKey = '';
        switch ($out['method']) {
            case 'get':
                //返回缓存数据的key
                $callCacheKey = md5("{$method}_" . md5(serialize($arguments)));   //函数名 + 参数md5作key
                break;

            case 'add':
            case 'update':
            case 'delete':
                //返回空的key
                $callCacheKey = '';

                break;

            default:
                break;
        }

        return $callCacheKey;
    }

    //重写getTablePre方法
    public function getTablePre() {
        return self::$appReadApi->getTablePre();
    }

    //重写表明前缀和数据库切换方法
    public function setTablePre($tablepre) {
        return self::$appReadApi->setTablePre($tablepre);
    }

    //重写表明前缀和数据库切换方法
    public function setDatabase($dbname) {
        return self::$appReadApi->setDatabase($tablepre);
    }

    public function setCacheTime($cacheTime = 1800) {
        $this->cacheTime = $cacheTime;
    }

    public function getCacheTime() {
        return $this->cacheTime;
    }

    public function setCacheHandler($memcache, $cacheTime = 1800) {
        $this->cacheTime = $cacheTime;
        $this->cache = $memcache;
    }

    public function set($key, $val, $cacheTime = 1800) {
        return $this->cache->set($key, $val, $cacheTime);
    }

    public function get($key) {
        return $this->cache->get($key);
    }

}
