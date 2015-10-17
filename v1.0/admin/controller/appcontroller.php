<?php
/**
 * App控制器基类
 * by lane
 * @2015-03-15
 * -----------------
 */
Class AppController extends Controller {

    /*public function __construct() {
        parent::__construct();
    }*/

    public function init() {
        //用户登录检查
        if (!isset($this->config[NOTNEEDLOGIN]) || !$this->config[NOTNEEDLOGIN]) {
            AppUtil::userLoginCheck();
        }

        if ($this->config[NEEDDB]) {
            //读取数据
            //$users = $this->dao_read->getUser();

            //设置视图访问变量
            //$this->setViewVar(compact('navigators'));
        }
    }
}