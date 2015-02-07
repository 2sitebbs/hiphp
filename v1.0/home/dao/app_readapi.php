<?php
/*
 * read dao implement
 * by lane @2014-05-08
 * -------------------
*/

class AppReadApis extends DAOReadApis {

    //implement the abstract function
    public static function getImplement($driver, $tablepre, $class = null) {/*{{{*/
        return parent::getImplement($driver, $tablepre, __CLASS__);
    }/*}}}*/



    //------------------自定义函数-----------------


    //------------------示例函数-------------------
    function getUserWithPassword($val, $order = '', $limit = '') {
        $pre = $this->tablepre;
        $table = "{$pre}user";
        $fields = '*';
        $condition = "password='{$val}'";
        return $this->wrapper->select($table, $fields, $condition, $order, $limit);
    }

}
