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

        //production check
        $isProduction = isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] == 'production' ? 1 : 0;

        //redis server for production
        $redisHost = '127.0.0.1';
        $redisPort = '6379';
        $redisPassword = '';

        //根据ip分配对应的缓存服务器
        if ($isProduction) {
            $myRedisServer = $this->getRedisServer();
            $redisHost = $myRedisServer['host'];
            $redisPassword = $myRedisServer['password'];
        }

        //memcache server for production
        $mmcacheHost = '127.0.0.1';
        $mmcachePort = 11211;
        $mmcacheUsername = '';
        $mmcachePassword = '';

        //根据ip分配对应的缓存服务器
        if ($isProduction) {
            $myMemServer = $this->getMemcacheServer();
            $mmcacheHost = $myMemServer['host'];
            $mmcacheUsername = $myMemServer['username'];
            $mmcachePassword = $myMemServer['password'];
        }

        //用域名 + 网站id做前缀
        $keyPre = $config[ROOTDOMAIN] . $config[SITEID];

        //mysql dao implement
        if ($config[NEEDDB]) {
            //dao implement
            if ($config[NEEDMMCACHED]) {    //优先使用memcached扩展
                $memcache = new MMCached($keyPre, $mmcacheHost, $mmcachePort, $mmcacheUsername, $mmcachePassword);
                $dao_read = AppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE], 'AppCacheRead');
                $dao_read->setCacheHandler($memcache);
            }else if ($config[NEEDMMCACHE]) {
                $memcache = new MMCache($keyPre);
                $dao_read = AppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE], 'AppCacheRead');
                $dao_read->setCacheHandler($memcache);
            }else if ($config[NEEDREDIS]) {
                $rediscache = new RedisCache($keyPre, $redisHost, $redisPort, $redisPassword);
                $dao_read = AppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE], 'AppCacheRead');
                $dao_read->setCacheHandler($rediscache);
            }

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
                $memcache = new MMCached($keyPre, $mmcacheHost, $mmcachePort, $mmcacheUsername, $mmcachePassword);
                $dao_mongo_read = MongoAppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE], 'MongoAppCacheRead', 'MongoAppReadApis');
                $dao_mongo_read->setCacheHandler($memcache);
            }else if ($config[NEEDMMCACHE]) {
                $memcache = new MMCache($keyPre);
                $dao_mongo_read = MongoAppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE], 'MongoAppCacheRead', 'MongoAppReadApis');
                $dao_mongo_read->setCacheHandler($memcache);
            }else if ($config[NEEDREDIS]) {
                $rediscache = new RedisCache($keyPre, $redisHost, $redisPort, $redisPassword);
                $dao_mongo_read = MongoAppCacheRead::getImplement($config[DBDRIVER_READ], $config[TABLEPRE], 'MongoAppCacheRead', 'MongoAppReadApis');
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

    //根据IP地址分流redis服务器
    protected function getRedisServer() {
        //为不同的用户分配不同的缓存服务器以提高并发
        //$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        //$arrNums = split('.', $ip);
        //$firstIp = $arrNums[0];
        //$serverKey = $firstIp % 2;

        $serverKey = 0;

        $servers = array(
            array(
                'host' => '127.0.0.1',
                'password' => '',
            ),
        );

        return $servers[$serverKey];
    }

    //根据IP地址分流memcache服务器
    protected function getMemcacheServer() {
        //为不同的用户分配不同的缓存服务器以提高并发
        //$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        //$arrNums = split('.', $ip);
        //$firstIp = $arrNums[0];
        //$serverKey = $firstIp % 2;

        $serverKey = 1;

        $servers = array(
            array(
                'host' => '127.0.0.1',
                'username' => '',
                'password' => '',
            ),
        );

        return $servers[$serverKey];
    }

    /**
    * 保存变量供视图使用
    * 调用方法：$this->setViewVar($key, $val);
    * 调用方法：$this->setViewVar(array('a'=>'x'));
    */
    protected function setViewVar($arr, $val = null) {
        global $pageData;
        Util::setViewVar($arr, $val);
        
        $this->pageData = $pageData;
        return $this->pageData;
    }

    //获取视图变量
    protected function getViewVars($key = '') {
        global $pageData;
        $this->pageData = $pageData;
        return empty($key) ? $this->pageData : @$this->pageData[$key];
    }

    //设置布局、视图目录和视图
    public function configView($viewName = 'index', $viewGroup = 'default', $layout = 'main', $theme = 'default') {
        global $config;
        $this->config[VIEWNAME] = $viewName;
        $this->config[VIEWGROUP] = $viewGroup;
        $this->config[LAYOUT] = $layout;
        $this->config[THEME] = $config[THEME] = $theme;
    }

    //退出控制器，且不渲染视图
    protected function quit($isAjax = false) {
        global $pageStartTime;
        //do something here before exit controller without render views

        //非Ajax请求，且debug开启则输出程序执行耗时
        if (!$isAjax && $pageStartTime && isset($this->config[DEBUG]) && $this->config[DEBUG]) {
            $pageEndTime = microtime(true);
            $pageTimeCost = $pageEndTime - $pageStartTime;
            $serverIp = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
            $ips = explode('.', $serverIp);
            echo "<!--{$pageTimeCost} {$ips[3]}  {$this->config[APPVERSION]}-->";
        }
        exit;
    }
    
    //输出json格式数据，并退出程序
    protected function json($data) {
        $jsonpCallback = isset($_GET['callback']) && !empty($_GET['callback']) ? htmlspecialchars($_GET['callback']) : false;

        //输出json格式数据
        if ($jsonpCallback) {
            //回调函数名特殊字符过滤
            $jsonpCallback = preg_replace('/\W/', '', $jsonpCallback);      //删除除下划线、英文字母和数字之外的字符

            header('Content-type: text/javascript');
            echo "$jsonpCallback(" . json_encode($data) . ")";
        }else {
            header('Content-type: application/json');
            echo json_encode($data);
        }

        //退出程序
        $this->quit('ajax');
    }

    //初始化
    public function init() {
        //do something
    }

    //在render视图之前做一些事情
    public function beforeRender() {
        //do something
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
