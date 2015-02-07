<?php
/*
 * constant
 * include const define
 * by lane @2012-01-01
*/
//basic
define('VERSION', 'version');
define('SITENAME', 'sitename');
define('ROOTDOMAIN', 'rootdomain');
define('BASEURL', 'baseurl');
define('PREKEY4PASSWORD', 'preKeyforPassword');
define('DEBUG', 'debug');

define('SESSIONUSER', 'sessionuser');
define('THEME', 'theme');
define('LAYOUT', 'layout');
define('VIEWGROUP', 'viewGroup');
define('VIEWNAME', 'viewName');

//for db
define('DBHOST', 'db_host');
define('DBUSER', 'db_username');
define('DBPASSWORD', 'db_password');
define('DATABASE', 'db_database');
define('DBPORT', 'db_port');

define('DBDRIVER', 'db_driver');
define('DBDRIVER_READ', 'db_driver_read');
define('DBDRIVER_WRITE', 'db_driver_write');
define('TABLEPRE', 'db_tablepre');
define('DBLINK', 'db_link');
define('DAOIMPL', 'db_daoimpl');

define('NEEDDB', 'db_needed');
define('NEEDMMCACHE', 'mmcache_needed');
define('NEEDMMCACHED', 'mmcached_needed');
define('NEEDREDIS', 'redis_needed');
define('NEEDSPHINX', 'sphinx_needed');
define('NEEDMONGODB', 'mongodb_needed');

//error msg for db init
define('DBCONNECTFAIL', 'dberror_dbconnectfail');
define('DBSELECTFAIL', 'dberror_dbselectfail');

//error
define('LOG', 'log');
define('INFO', 'info');
define('WARN', 'warn');
define('ERROR', 'error');
define('FATAL', 'fatal');