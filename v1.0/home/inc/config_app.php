<?php
/**
 * app config file
 * created by lane
 * @2014-05-09
*/
if (!defined('SUPERUSER')) {define('SUPERUSER', 'superuser');}
if (!defined('SUPERUSERPASSWORD')) {define('SUPERUSERPASSWORD', 'superuserpassword');}
if (!defined('SITEID')) {define('SITEID', 'siteappid');}
if (!defined('SITEIDS')) {define('SITEIDS', 'siteappids');}
if (!defined('SITENAMES')) {define('SITENAMES', 'sitenames');}
if (!defined('URLREWRITE')) {define('URLREWRITE', 'urlrewrite');}


$config_app = array(
    SITENAME => 'HiPHP前端示例',
    SITENAMES => array(
            LANG_ZHCN => 'HiPHP前端示例',
            LANG_ZHTW => 'HiPHP前端示例',
            LANG_EN => 'HiPHP',
        ),

    VERSION => '1.0',
    //ROOTDOMAIN => (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'hiphp.2sitebbs.com'),
    THEME => (isset($_GET['theme']) ? $_GET['theme'] : 'default'),         //皮肤名

    //此密码前缀请务必修改！！
    PREKEY4PASSWORD => 'hiphp_',

    /*{{{*/
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


    //mongodb配置
    MONGODBDRIVER_READ => array(    //mongodb数据帐号配置
        DBHOST => 'localhost',
        DBUSER => 'test',
        DBPASSWORD => '123456',
        DATABASE => 'test',
        DBPORT => '27017',
    ),
    MONGODBDRIVER_WRITE => array(    //mongodb数据帐号配置
        DBHOST => 'localhost',
        DBUSER => 'test',
        DBPASSWORD => '123456',
        DATABASE => 'test',
        DBPORT => '27017',
    ),


    DEBUG => true,       //开启debug模式
    /*}}}*/

    /*单独网站配置*/
    SITEID => 96,   //网站ID
    SITEIDS => array(
            LANG_ZHCN => '96',
            LANG_ZHTW => '96',
            LANG_EN => '97',
        ),

    URLREWRITE => false,       //开启nginx url rewrite
);

//merge config
$config = isset($config) ? array_merge($config, $config_app) : $config_app;

//重置语言对应的网站
if (isset($lang) && isset($config[SITEIDS][$lang])) {
    $config[SITEID] = $config[SITEIDS][$lang];
    $config[SITENAME] = $config[SITENAMES][$lang];
}
