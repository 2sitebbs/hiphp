<?php
/**
 * 默认控制器
 * by lane
 * @2015-01-13
 * -----------------
 */
$pageStartTime = microtime(true);

//page config
$config[NEEDDB] = true;          //mysql支持
$config[NEEDMMCACHE] = false;    //memcache支持
$config[NEEDMMCACHED] = false;   //使用扩展memcached连memcache
$config[NEEDREDIS] = false;      //redis支持
$config[NEEDSPHINX] = false;     //sphinx全文索引支持
$config[NEEDMONGODB] = false;    //mongodb支持

//后台设置
//$config[NOTNEEDLOGIN] = true;    //不需要登录


/**----------------
 * controll logical code here
 * {{{
 */
Class DefaultController extends AppController {

    //默认动作
    public function index() {
        //从mysql读数据
        $users = $this->dao_read->getUser();

        //设置视图访问变量
        $this->setViewVar('users', $users);

        //网页SEO内容设置
        $pageName = 'default';
        $pageTitle = 'HiPHP后台演示控制器';
        $pageDescription = 'HiPHP后台演示控制器';
        $pageKeywords = 'HiPHP, 后台演示, 控制器';

        $this->setViewVar(compact('pageName', 'pageTitle', 'pageDescription', 'pageKeywords'));
    }

    //默认动作
    public function test() {
        header("Content-type:text/html; charset=utf-8");
        echo '<h1>Welcome to admin</h1>';
        echo '<pre>';
        print_r($this);
        echo '</pre>';
        $this->quit();
    }

}
/**
 * }}}
 */
