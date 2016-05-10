<?php
/**
 * Cache DAO Test控制器
 * by lane
 * @2016-03-20
 * -----------------
 */
$pageStartTime = microtime(true);   //执行计时

//初始化配置
$config[NEEDDB] = true;          //mysql支持
$config[NEEDMMCACHE] = true;     //memcache支持
$config[NEEDMMCACHED] = false;   //使用扩展memcached连memcache
$config[NEEDREDIS] = false;      //redis支持
$config[NEEDSPHINX] = false;     //sphinx全文索引支持
$config[NEEDMONGODB] = true;    //mongodb支持


/**----------------
 * controll logical code here
 * {{{
 */
Class TestCacheController extends AppController{

    //默认动作，示例action
    public function index() {
        $now = date('Y-m-d H:i:s');
        echo "[{$now}]\tCache dao test.\n";
    }

    //cache time test
    public function cacheTime() {
        $now = date('Y-m-d H:i:s');
        echo "[{$now}]\tCache time test.\n";


        $cacheTime = $this->dao_read->getCacheTime();
        echo "[{$now}]\tOrigin cache time is [{$cacheTime}].\n";

        $this->dao_read->setCacheTime(1800);
        $cacheTime = $this->dao_read->getCacheTime();
        echo "[{$now}]\tCache time after set [{$cacheTime}].\n";
        

    }


}
/**
 * }}}
 */
