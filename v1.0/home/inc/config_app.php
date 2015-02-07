<?php
/**
 * app config file
 * created by lane
 * @2014-05-09
*/
if (!defined('SUPERUSER')) {define('SUPERUSER', 'superuser');}
if (!defined('SUPERUSERPASSWORD')) {define('SUPERUSERPASSWORD', 'superuserpassword');}

$config_app = array(
    SITENAME => 'hiphp前端示例',
    VERSION => '1.0',
    //ROOTDOMAIN => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'hiphp.2sitebbs.com'),
    THEME => 'default',         //皮肤名

    //此密码前缀请务必修改！！
    PREKEY4PASSWORD => 'hiphp_',/*{{{*/

    TABLEPRE => '',             //表名前缀
    DBDRIVER_READ => array(     //读数据帐号配置
        DBHOST => 'localhost',
        DBUSER => 'test',
        DBPASSWORD => '123456',
        DATABASE => 'test',
        DBPORT => '3306',
    ),

    DBDRIVER_WRITE => array(    //写数据帐号配置
        DBHOST => 'localhost',
        DBUSER => 'test',
        DBPASSWORD => '123456',
        DATABASE => 'test',
        DBPORT => '3306',
    ),

    DEBUG => true,/*}}}*/       //开启debug模式

    /*新增配置项目*/
    // SUPERUSER => 'admin',
    // SUPERUSERPASSWORD => 'phpwebadmin',
);

//merge config
$config = isset($config) ? array_merge($config, $config_app) : $config_app;