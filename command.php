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
$_SERVER['HTTP_X_REAL_IP'] = '127.0.0.1';

//全局变量设置
$currentDir = dirname(__FILE__);
$_config[APPCONTEXT] = "{$currentDir}/{$_config[APPVERSION]}";
$_config[THEMECONTEXT] = "{$currentDir}/{$_config[APPVERSION]}";
$_config[THEMEDIR] = "{$_config[APPVERSION]}/{$_config[APPMAIN]}";


//尝试加载控制器中的动作
$includeOk = @include_once "{$_config[APPCONTEXT]}/{$_config[APPMAIN]}/controller/{$_config[APPCONTROLLERGROUP]}{$_config[APPCONTROLLER]}.php";
if (!$includeOk) {
    $errorMsg = <<<eof
    Controller {$_config[APPCONTEXT]}/{$_config[APPMAIN]}/controller/{$_config[APPCONTROLLER]}.php not exists.\n
eof;
    die($errorMsg);
}


//call action
$appController = new AppController();
$appController->configView($_config[APPACTION], $_config[APPCONTROLLER]);  //设置默认视图目录和默认视图
$appController->$_config[APPACTION]();  //执行控制器
