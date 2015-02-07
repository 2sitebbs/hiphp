<?php
/**
 * 控制器基类
 * by lane
 * @2015-01-13
 * -----------------
 */
Class Controller {
    protected $pageData;    //所有视图所需要的数据保存对象
    protected $config;      //App配置信息

    protected $dao_read;    //mysql读数据对象、memcache读写对象
    protected $dao_write;   //mysql写数据对象
    protected $dao_mongo_read;  //mongodb读数据对象
    protected $dao_mongo_write; //mongodb写数据对象

    //初始化DAO对象等全局变量
    public function __construct() {
        global $pageData, $config;

        //初始化变量
        $dao_read = $dao_write = $dao_mongo_read = $dao_mongo_write = null;

        //dao implement
        if ($config[NEEDMMCACHED]) {    //优先使用memcached扩展
            $memcache = new MMCached($config[ROOTDOMAIN]);
            $dao_read = AppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
            $dao_read->setCacheHandler($memcache);
        }else if ($config[NEEDMMCACHE]) {
            $memcache = new MMCache($config[ROOTDOMAIN]);
            $dao_read = AppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
            $dao_read->setCacheHandler($memcache);
        }else if ($config[NEEDREDIS]) {
            $rediscache = new RedisCache($config[ROOTDOMAIN]);
            $dao_read = AppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
            $dao_read->setCacheHandler($rediscache);
        }

        //mysql dao implement
        if ($config[NEEDDB]) {
            if ((!$config[NEEDMMCACHED] && !$config[NEEDMMCACHE] && !$config[NEEDREDIS]) || !isset($dao_read)) {
                $dao_read = AppReadApis::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
            }
            $dao_write = AppWriteApis::getImplement($config[DBDRIVER_WRITE], $config[TABLEPRE]);
        }

        //mongodb实例化
        if ($config[NEEDMONGODB]) {
            if (!$config[NEEDMMCACHED] && !$config[NEEDMMCACHE] && !$config[NEEDREDIS]) {
                $dao_mongo_read = MongoAppReadApis::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
            }else if ($config[NEEDMMCACHED]) {
                $memcache = new MMCached($config[ROOTDOMAIN]);
                $dao_mongo_read = MongoAppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
                $dao_mongo_read->setCacheHandler($memcache);
            }else if ($config[NEEDMMCACHE]) {
                $memcache = new MMCache($config[ROOTDOMAIN]);
                $dao_mongo_read = MongoAppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
                $dao_mongo_read->setCacheHandler($memcache);
            }else if ($config[NEEDREDIS]) {
                $rediscache = new RedisCache($config[ROOTDOMAIN]);
                $dao_mongo_read = MongoAppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE]);
                $dao_mongo_read->setCacheHandler($rediscache);
            }
            $dao_mongo_write = MongoAppWriteApis::getImplement($config[DBDRIVER_WRITE], $config[TABLEPRE]);
        }

        $this->pageData = $pageData;
        $this->config = $config;
        $this->dao_read = $dao_read;
        $this->dao_write = $dao_write;
        $this->dao_mongo_read = $dao_mongo_read;
        $this->dao_mongo_write = $dao_mongo_write;
    }

    //设置布局、视图目录和视图
    public function configView($viewName = 'index', $viewGroup = 'default', $layout = 'main') {
        $this->config[VIEWNAME] = $viewName;
        $this->config[VIEWGROUP] = $viewGroup;
        $this->config[LAYOUT] = $layout;
    }

    //退出控制器，且不渲染视图
    public function quit($isAjax = false) {
        global $pageStartTime;
        //do something here before exit controller without render views

        //非Ajax请求，且debug开启则输出程序执行耗时
        if (!$isAjax && $pageStartTime && isset($this->config[DEBUG]) && $this->config[DEBUG]) {
            $pageEndTime = microtime(true);
            $pageTimeCost = $pageEndTime - $pageStartTime;
            echo "<!-- {$pageTimeCost} -->";
        }
        exit;
    }

    //控制器执行的最后一步，渲染视图
    public function render() {
        //do something here before render

        Util::render(
                    $this->config[VIEWGROUP],
                    $this->config[VIEWNAME],
                    $this->config[LAYOUT],
                    $this->config[THEME]
                );
    }

    //默认动作
    public function index() {
        echo '<h1>Welcome to use hiphp</h1>';
    }

}