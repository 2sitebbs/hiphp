<?php
/**
 * 默认控制器
 * by lane
 * @2015-02-07
 * -----------------
 */
$pageStartTime = microtime(true);   //执行计时

//初始化配置
$config[NEEDDB] = true;          //mysql支持
$config[NEEDMMCACHE] = true;    //memcache支持
$config[NEEDMMCACHED] = false;   //使用扩展memcached连memcache
$config[NEEDREDIS] = false;      //redis支持
$config[NEEDSPHINX] = false;     //sphinx全文索引支持
$config[NEEDMONGODB] = false;    //mongodb支持


/**----------------
 * controll logical code here
 * {{{
 */
Class DefaultController extends AppController {

    //默认动作，示例action
    public function index() {
        //print_r($this);exit;

        //从mysql读数据
        $users = $this->dao_read->getUser();

        //设置视图访问变量
        $this->setViewVar('users', $users);

        //网页SEO内容设置
        $pageName = 'default';
        $pageTitle = 'HiPHP前端演示控制器';
        $pageDescription = 'HiPHP前端演示控制器';
        $pageKeywords = 'HiPHP, 前端演示, 控制器';

        $this->setViewVar(compact('pageName', 'pageTitle', 'pageDescription', 'pageKeywords'));

        //设置渲染视图
        //$this->configView('demo');

        //不渲染视图
        /*echo "hello";
        $this->quit();*/

        /*echo '{"code":1}';
        $this->quit('ajax');*/
    }

    //测试动作
    public function test() {
        echo '<h1>Welcome to home</h1>';
        echo '<pre>';
        print_r($this->config);
        echo '</pre>';

        $this->quit();
    }

}
/**
 * }}}
 */
