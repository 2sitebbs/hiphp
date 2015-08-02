<?php
/*
 * basic class
 * by lane @2013-09-20
*/

if (!defined('DBHOST')) {define('DBHOST', 'db_host');}
if (!defined('DBUSER')) {define('DBUSER', 'db_username');}
if (!defined('DBPASSWORD')) {define('DBPASSWORD', 'db_password');}
if (!defined('DATABASE')) {define('DATABASE', 'db_database');}
if (!defined('DBPORT')) {define('DBPORT', 'db_port');}

if (!defined('DBDRIVER')) {define('DBDRIVER', 'db_driver');}
if (!defined('TABLEPRE')) {define('TABLEPRE', 'db_tablepre');}
if (!defined('DBLINK')) {define('DBLINK', 'db_link');}
if (!defined('DAOIMPL')) {define('DAOIMPL', 'db_daoimpl');}

class Base {

    function echoError($msg) {
        header('Content-type: text/html; charset=utf-8');
        echo $msg;
    }

}
