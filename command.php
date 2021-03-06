<?php
/**
 * 命令行入口程序
 * by lane
 * @2015-03-15
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
if (count($argv) <= 1) {
    die("Usage: php command.php controller action [appMain] [version] [parameters] [controllergroup]\n");
}

$pageStartTime = microtime(true);   //执行计时
require_once dirname(__FILE__) . '/core/inc/constant.php';

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
    APPCONTROLLER => (isset($argv[1]) ? htmlspecialchars(trim($argv[1])) : 'default'),
    APPACTION => (isset($argv[2]) ? htmlspecialchars(trim($argv[2])) : 'index'),
    APPMAIN => (isset($argv[3]) && !empty($argv[3]) ? $argv[3] : 'home'),
    APPVERSION => (isset($argv[4]) ? htmlspecialchars(trim($argv[4])) : 'v1.0'),
    APPCONTROLLERGROUP => (isset($argv[6]) && !empty($argv[6]) ? htmlspecialchars(trim(str_replace('/', '', $argv[6]))) . '/' : ''),
);

//GET传参解析
$paraStr = isset($argv[5]) && !empty($argv[5]) ? $argv[5] : '';
$arrTmp = explode('&', $paraStr);
foreach ($arrTmp as $item) {
    $arr = explode('=', $item);
    if (isset($arr[0]) && isset($arr[1])) {
        $_REQUEST[$arr[0]] = $arr[1];
        $_GET[$arr[0]] = $arr[1];
    }
}

//环境变量设置
$_SERVER['HTTP_X_REAL_IP'] = '127.0.0.1';
$_SERVER['HTTP_HOST'] = isset($_GET['host']) ? $_GET['host'] : '127.0.0.1';
$_SERVER['REQUEST_URI'] = "/{$_config[APPCONTROLLER]}_{$_config[APPACTION]}.html";


//是否固定版本号
$fixedVersion = isset($_GET['fixedv']) ? (int)$_GET['fixedv'] : 0;

//全局变量设置
$currentDir = dirname(__FILE__);
$_config[APPCONTEXT] = "{$currentDir}/{$_config[APPVERSION]}";
$_config[THEMECONTEXT] = "{$currentDir}/{$_config[APPVERSION]}";
$_config[THEMEDIR] = "{$_config[APPVERSION]}/{$_config[APPMAIN]}";


//尝试加载控制器
$controllerFile = "{$_config[APPCONTEXT]}/{$_config[APPMAIN]}/controller/{$_config[APPCONTROLLERGROUP]}{$_config[APPCONTROLLER]}.php";
if (!file_exists($controllerFile)) {
    $errorMsg = <<<eof
    Controller {$_config[APPCONTROLLER]}.php not exists.

eof;
    echo $errorMsg;
    exit;
}

//初始化
$libPath = "{$currentDir}/core";          //设定HIPHP的路径，可将core保存到一个公用目录以便多个项目使用
require_once "{$libPath}/inc/hiphp.php";     //引入HIPHP
require_once "{$_config[APPCONTEXT]}/{$_config[APPMAIN]}/inc/init_app.php";		//包含app所需文件

//尝试加载控制器中的动作
$includeOk = include_once $controllerFile;
if (!$includeOk) {
    $errorMsg = <<<eof
    Controller {$_config[APPCONTROLLER]}.php not exists.\n
eof;
    die($errorMsg);
}

//合并配置
$config = isset($_config) ? array_merge($_config, $config) : $config;

//call action
$controllerName = ucfirst(strtolower($_config[APPCONTROLLER])) . "Controller";
$appController = new $controllerName();
$appController->init();                 //初始化
$appController->$_config[APPACTION]();  //执行控制器
