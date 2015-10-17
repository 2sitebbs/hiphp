<?php
/**
 * App控制器基类
 * by lane
 * @2015-03-15
 * -----------------
 */
Class AppController extends Controller {

    //App控制器初始化函数
    public function init() {
        if ($this->config[NEEDDB]) {
            //读取数据
            //$users = $this->dao_read->getUser();

            //设置视图访问变量
            //$this->setViewVar(compact('navigators'));
        }
    }
}
