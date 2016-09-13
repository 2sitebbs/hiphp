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

    //解析比较运算符，得到where条件中的操作符
    protected function parseOperator($value) {
        $opArr = array(
            '=',
            '>',
            '<',
            '<=',
            '>=',
            '!=',
        );
        $reg = '/^(?:(=|>|<|>=|<=|!=) )?(.+)$/i';

        $value = htmlspecialchars_decode($value, ENT_QUOTES);
        preg_match($reg, $value, $match);

        $out = array();
        if (count($match) == 3 && !empty($match[1]) && in_array($match[1], $opArr)) {   //有操作符
            $out['op'] = $match[1];
            $out['value'] = $match[2] == '""' || $match[2] == "''" ? '' : htmlspecialchars($match[2], ENT_QUOTES);
        }else {
            $out['op'] = '=';
            $out['value'] = htmlspecialchars($value, ENT_QUOTES);
        }

        return $out;
    }

}
