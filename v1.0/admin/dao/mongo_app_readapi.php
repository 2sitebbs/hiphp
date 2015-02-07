<?php
/*
 * read dao implement
 * by lane @2014-11-19
 * -------------------
*/

class MongoAppReadApis extends MongoDAOReadApis {

    //implement the abstract function
    public static function getImplement($driver, $tablepre, $class = null) {/*{{{*/
        return parent::getImplement($driver, $tablepre, __CLASS__);
    }/*}}}*/



    //------------------自定义函数-----------------


    //------------------示例函数-------------------
    function getQATaskWithTaskname($val, $order = '', $limit = '') {
        $pre = $this->tablepre;
        $table = "{$pre}qatask";
        $fields = array();              //注意与mysql的区别，查询字段为数组
        $condition['taskname'] = $val;  //注意与mysql的区别，查询条件为key=>value形式的数组
        return $this->wrapper->select($table, $fields, $condition, $order, $limit);
    }

}
