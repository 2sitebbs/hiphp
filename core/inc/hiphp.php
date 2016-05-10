<?php
date_default_timezone_set('Asia/Hong_Kong');

$_incPath = dirname(__FILE__);
require_once "{$_incPath}/controller.php";
require_once "{$_incPath}/constant.php";
require_once "{$_incPath}/util.php";
require_once "{$_incPath}/restapi.php";
require_once "{$_incPath}/config.php";
require_once "{$_incPath}/../dao/api_read.php";
require_once "{$_incPath}/../dao/api_write.php";
require_once "{$_incPath}/../dao/mongo_api_read.php";
require_once "{$_incPath}/../dao/mongo_api_write.php";
require_once "{$_incPath}/../dao/cache.php";
require_once "{$_incPath}/../dao/cached.php";
require_once "{$_incPath}/../dao/redis.php";
require_once "{$_incPath}/../dao/cache_io.php";


//enable error_reporting
if ( !isset($_SERVER['APP_ENV']) || ($_SERVER['APP_ENV'] != 'production' && $config[DEBUG]) ) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', TRUE);
}


//data for views
$pageData = array();

/*
//错误输出处理
function error_handler($errno , $errstr , $errfile , $errline , $errcontext) {
    $context = print_r($errcontext, true);
    $error = <<<eof

错误码：{$errno}
错误信息：{$errstr}
错误文件：{$errfile}
错误行号：{$errline}
错误上下文：{$context}

eof;
    @error_log("ERROR----------: \n{$error}\n", 3, '/var/log/debug.log');
}
set_error_handler('error_handler');
*/