<?php
/*
 * app init
 * by lane @2014-04-01
 * -----------------------
*/
session_start();

//require hiphp lib
$appIncPath = dirname(__FILE__);            //当前文件所在目录
$appPath = "{$appIncPath}/../www";          //设定项目根目录，便于加载视图

//引入语言包
$supportedLang = array(
    LANG_ZHCN,
    LANG_ZHTW,
    LANG_EN,
);
$lang = isset($_GET['lang']) && in_array($_GET['lang'], $supportedLang) ? strtolower($_GET['lang']) : Util::getHttpLang();
require_once "{$appIncPath}/../lang/{$lang}.php";

//保存视图变量
Util::setViewVar('lang', $lang);

//require config and extend classes
require_once "{$appIncPath}/config_app.php";
require_once "{$appIncPath}/util_app.php";
require_once "{$appIncPath}/../dao/app_readapi.php";
require_once "{$appIncPath}/../dao/app_writeapi.php";
require_once "{$appIncPath}/../dao/app_cacheread.php";

//mongodb support
require_once "{$appIncPath}/../dao/mongo_app_readapi.php";
require_once "{$appIncPath}/../dao/mongo_app_writeapi.php";
require_once "{$appIncPath}/../dao/mongo_app_cacheread.php";

//app controller
require_once "{$appIncPath}/../controller/appcontroller.php";
