<?php
/*
 * dao implement basic class
 * by lane @2013-08-21
 * ----------------------
 * 添加切换数据库的接口 @2014-07-22
 * mongodb专用实例类
*/
require_once 'mongo_wrapper.php';

abstract class MongoDAOImplement {
    protected $tablepre;
    protected $wrapper;
    protected static $impl;     //用于保存实例对象的数组

    protected function __construct($driver, $tablepre) {
        //always get a new wrapper
        $this->wrapper = MongoDAOWrapper::getWrapper($driver, true);
        $this->tablepre = $tablepre;
    }

    function __toString() {
        return "DAO implements.";
    }

    public function setTablePre($tablepre) {
        $this->tablepre = $tablepre;
    }

    public function getTablePre() {
        return $this->tablepre;
    }

    public function setDatabase($dbname) {
        return $this->wrapper->select_db($dbname);
    }

    /* abstract functions */
    abstract protected function parseMethod($method);
    public static function getImplement($driver, $tablepre, $class = null){return null;}

}
