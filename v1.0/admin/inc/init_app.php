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
