<?php
/*
 * constant
 * include const define
 * by lane @2012-01-01
*/
//basic
if (!defined('VERSION')) {define('VERSION', 'version');}
if (!defined('SITENAME')) {define('SITENAME', 'sitename');}
if (!defined('ROOTDOMAIN')) {define('ROOTDOMAIN', 'rootdomain');}
if (!defined('BASEURL')) {define('BASEURL', 'baseurl');}
if (!defined('PREKEY4PASSWORD')) {define('PREKEY4PASSWORD', 'preKeyforPassword');}
if (!defined('DEBUG')) {define('DEBUG', 'debug');}

if (!defined('SESSIONUSER')) {define('SESSIONUSER', 'sessionuser');}
if (!defined('THEME')) {define('THEME', 'theme');}
if (!defined('LAYOUT')) {define('LAYOUT', 'layout');}
if (!defined('VIEWGROUP')) {define('VIEWGROUP', 'viewGroup');}
if (!defined('VIEWNAME')) {define('VIEWNAME', 'viewName');}

//for db
if (!defined('DBHOST')) {define('DBHOST', 'db_host');}
if (!defined('DBUSER')) {define('DBUSER', 'db_username');}
if (!defined('DBPASSWORD')) {define('DBPASSWORD', 'db_password');}
if (!defined('DATABASE')) {define('DATABASE', 'db_database');}
if (!defined('DBPORT')) {define('DBPORT', 'db_port');}

if (!defined('DBDRIVER')) {define('DBDRIVER', 'db_driver');}
if (!defined('DBDRIVER_READ')) {define('DBDRIVER_READ', 'db_driver_read');}
if (!defined('DBDRIVER_WRITE')) {define('DBDRIVER_WRITE', 'db_driver_write');}
if (!defined('TABLEPRE')) {define('TABLEPRE', 'db_tablepre');}
if (!defined('DBLINK')) {define('DBLINK', 'db_link');}
if (!defined('DAOIMPL')) {define('DAOIMPL', 'db_daoimpl');}

if (!defined('NEEDDB')) {define('NEEDDB', 'db_needed');}
if (!defined('NEEDMMCACHE')) {define('NEEDMMCACHE', 'mmcache_needed');}
if (!defined('NEEDMMCACHED')) {define('NEEDMMCACHED', 'mmcached_needed');}
if (!defined('NEEDREDIS')) {define('NEEDREDIS', 'redis_needed');}
if (!defined('NEEDSPHINX')) {define('NEEDSPHINX', 'sphinx_needed');}
if (!defined('NEEDMONGODB')) {define('NEEDMONGODB', 'mongodb_needed');}
if (!defined('NOTNEEDLOGIN')) {define('NOTNEEDLOGIN', 'controller_notNeedLogin');}

//error msg for db init
if (!defined('DBCONNECTFAIL')) {define('DBCONNECTFAIL', 'dberror_dbconnectfail');}
if (!defined('DBSELECTFAIL')) {define('DBSELECTFAIL', 'dberror_dbselectfail');}

//error
if (!defined('LOG')) {define('LOG', 'log');}
if (!defined('INFO')) {define('INFO', 'info');}
if (!defined('WARN')) {define('WARN', 'warn');}
if (!defined('ERROR')) {define('ERROR', 'error');}
if (!defined('FATAL')) {define('FATAL', 'fatal');}


if (!defined('SUPERUSER')) {define('SUPERUSER', 'superuser');}
if (!defined('SUPERUSERPASSWORD')) {define('SUPERUSERPASSWORD', 'superuserpassword');}
if (!defined('SITEID')) {define('SITEID', 'siteappid');}
if (!defined('SITEIDS')) {define('SITEIDS', 'siteappids');}
if (!defined('SITENAMES')) {define('SITENAMES', 'sitenames');}
if (!defined('URLREWRITE')) {define('URLREWRITE', 'urlrewrite');}

if (!defined('COOKIEUID')) {define('COOKIEUID', 'cookieuid');}
if (!defined('YUNAPPSECRET')) {define('YUNAPPSECRET', 'xinyuemin.com_20141004');}



if (!defined('APPNAME')) {define('APPNAME', 'appname');}
if (!defined('VERSION')) {define('VERSION', 'appversion');}