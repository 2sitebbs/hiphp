<?php
/**
 * 前端统一入口程序
 * by lane
 * @2015-01-13
 * -----------------
 * 主要参数：
 * version ==> 版本号
 * controller ==> 控制器
 * controllergroup ==> 控制器目录
 * action ==> 动作
 * -----------------
 * 主要全局变量：
 * APPMAIN ==> 程序入口
 * APPCONTEXT ==> 程序上下文路径
 * THEMECONTEXT ==> 皮肤上下文路径
 * THEMEDIR ==> 皮肤前端展示路径
 */
$pageStartTime = microtime(true);   //执行计时

require_once dirname(__FILE__) . '/../core/inc/constant.php';

if (!defined('APPVERSION')) {define('APPVERSION', 'global_app_version');}
if (!defined('APPCONTROLLER')) {define('APPCONTROLLER', 'global_app_controller');}
if (!defined('APPCONTROLLERGROUP')) {define('APPCONTROLLERGROUP', 'global_app_controllergroup');}
if (!defined('APPACTION')) {define('APPACTION', 'global_app_action');}

if (!defined('APPMAIN')) {define('APPMAIN', 'global_app_main');}
if (!defined('APPCONTEXT')) {define('APPCONTEXT', 'global_app_context');}
if (!defined('THEMECONTEXT')) {define('THEMECONTEXT', 'global_theme_context');}
if (!defined('THEMEDIR')) {define('THEMEDIR', 'global_theme_dir');}


//获取app的版本号、控制器和动作
$_config = array(
    APPVERSION => (isset($_GET['version']) ? htmlspecialchars(trim($_GET['version'])) : 'v1.0'),
    APPCONTROLLER => (isset($_GET['controller']) ? htmlspecialchars(trim($_GET['controller'])) : 'default'),
    APPCONTROLLERGROUP => (isset($_GET['controllergroup']) && !empty($_GET['controllergroup']) ? htmlspecialchars(trim(str_replace('/', '', $_GET['controllergroup']))) . '/' : ''),
    APPACTION => (isset($_GET['action']) ? htmlspecialchars(trim($_GET['action'])) : 'index'),

    APPMAIN => (isset($appMain) && !empty($appMain) ? $appMain : 'home'),
);

$_config[APPVERSION] = isset($_GET['v']) ? htmlspecialchars(trim($_GET['v'])) : $_config[APPVERSION];

//是否固定版本号
$fixedVersion = isset($_GET['fixedv']) ? (int)$_GET['fixedv'] : 0;

//全局变量设置
$currentDir = dirname(__FILE__);	//设定项目根目录，便于加载视图        
$_config[APPCONTEXT] = "{$currentDir}/../{$_config[APPVERSION]}";
$_config[THEMECONTEXT] = "{$currentDir}/{$_config[APPVERSION]}";
$_config[THEMEDIR] = "{$_config[APPVERSION]}/{$_config[APPMAIN]}";

//统一配置文件存放目录，支持从配置文件中设置版本号
$sid = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : 1;
$subSiteConfig = "{$_config[APPCONTEXT]}/{$_config[APPMAIN]}/inc/sites/config_sid_{$sid}.php";
if (!$fixedVersion && file_exists($subSiteConfig)) {
    require $subSiteConfig;             //包含app所需文件
    if (isset($config[VERSION]) && $config[VERSION] != $_config[APPVERSION]) {
        $_config[APPVERSION] = $config[VERSION];
        $_config[APPCONTEXT] = "{$currentDir}/../{$_config[APPVERSION]}";
        $_config[THEMECONTEXT] = "{$currentDir}/{$_config[APPVERSION]}";
        $_config[THEMEDIR] = "{$_config[APPVERSION]}/{$_config[APPMAIN]}";
    }
}


//尝试加载控制器
$controllerFile = "{$_config[APPCONTEXT]}/{$_config[APPMAIN]}/controller/{$_config[APPCONTROLLERGROUP]}{$_config[APPCONTROLLER]}.php";
if (!file_exists($controllerFile)) {
    $errorMsg = <<<eof
    Controller {$_config[APPCONTROLLER]}.php not exists.

eof;
    die($errorMsg);
}
$libPath = "{$currentDir}/../core";          //设定hiphp的路径，可将core保存到一个公用目录以便多个项目使用
require_once "{$libPath}/inc/hiphp.php";     //引入hiphp
require_once "{$_config[APPCONTEXT]}/{$_config[APPMAIN]}/inc/init_app.php";		//包含app所需文件

$includeOk = include_once $controllerFile;
if (!$includeOk) {
    $errorMsg = <<<eof
    Controller {$_config[APPCONTROLLER]}.php not exists.

eof;
    die($errorMsg);
}


//合并配置
$config = isset($_config) ? array_merge($_config, $config) : $config;


//执行控制器中的动作
$controllerName = ucfirst(strtolower($_config[APPCONTROLLER])) . "Controller";
$appController = new $controllerName();             //实例化控制器 
$appController->configView($_config[APPACTION], $_config[APPCONTROLLER], 'main', $config[THEME]);  //设置默认视图目录和默认视图
$appController->init();                                                 //初始化控制器
$action = $_config[APPACTION];
$appController->$action();  	                        //执行控制器
$appController->render();             		            //渲染视图
