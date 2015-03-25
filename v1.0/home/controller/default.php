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
$config[NEEDMMCACHE] = false;    //memcache支持
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
        Util::setViewVar('users', $users);

        //网页SEO内容设置
        $pageName = 'default';
        $pageTitle = 'HiPHP前端演示控制器';
        $pageDescription = 'HiPHP前端演示控制器';
        $pageKeywords = 'HiPHP, 前端演示, 控制器';

        Util::setViewVar(compact('pageName', 'pageTitle', 'pageDescription', 'pageKeywords'));

        //设置渲染视图
        //$this->configView('demo');

        //不渲染视图
        /*echo "hello";
        $this->quit();*/

        /*echo '{"code":1}';
        $this->quit('ajax');*/

        //如果不主动渲染视图，
        //默认使用main布局，当前控制器同名视图目录，当前action同名视图
        /*
        $themeName = $this->config[THEME];
        $layoutName = 'main';
        $viewGroup = 'default';
        $viewName = 'index';
        Util::render($viewGroup, $viewName, $layoutName, $themeName);
        */
    }

    //测试动作
    public function test() {
        echo '<h1>Welcome to home</h1>';
        echo '<pre>';
        print_r($this);
        echo '</pre>';

        $this->quit();
    }

}
/**
 * }}}
 */