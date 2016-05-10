<?php
/*
 * dao implement basic class
 * by lane @2013-08-21
 * ----------------------
 * 添加切换数据库的接口 @2014-07-22
*/
abstract class DAOImplement {
    protected $tablepre;
    protected $wrapper;
    protected static $impl;     //用于保存实例对象的数组

    protected function __construct($driver, $tablepre) {
        //always get a new wrapper
        $this->wrapper = DAOWrapper::getWrapper($driver, true);
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
    
    public function close() {
        return $this->wrapper->close();
    }

    /* abstract functions */
    abstract protected function parseMethod($method);
    public static function getImplement($driver, $tablepre, $class = null){return null;}

}
