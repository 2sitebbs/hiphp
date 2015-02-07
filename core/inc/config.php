<?php
/**
 * config file
 * created by lane
 * @2013-09-02
 * -----------------
*/
require_once dirname(__FILE__) . '/constant.php';

$config = array(
    SITENAME => 'hiphp',
    VERSION => '1.0',
    ROOTDOMAIN => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'hiphp.2sitebbs.com'),
    THEME => 'default',
    LAYOUT => 'main',
    VIEWGROUP => 'default',
    VIEWNAME => 'index',

    PREKEY4PASSWORD => 'hiphp_',/*{{{*/
    TABLEPRE => 'qa_',

    DBDRIVER_READ => array(
        DBHOST => 'localhost',
        DBUSER => 'root',
        DBPASSWORD => '123456',
        DATABASE => 'test',
        DBPORT => '3306',
    ),

    DBDRIVER_WRITE => array(
        DBHOST => 'localhost',
        DBUSER => 'root',
        DBPASSWORD => '123456',
        DATABASE => 'test',
        DBPORT => '3306',
    ),

    NEEDDB => false,
    NEEDMMCACHE => false,
    NEEDMMCACHED => false,
    NEEDREDIS => false,
    NEEDSPHINX => false,
    NEEDMONGODB => false,

    DEBUG => true,/*}}}*/

    /*新增配置项目*/

);