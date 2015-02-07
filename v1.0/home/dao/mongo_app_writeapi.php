<?php
/*
 * write dao implement
 * by lane @2014-11-19
 * -------------------
*/

class MongoAppWriteApis extends MongoDAOWriteApis {

    //implement the abstract function
    public static function getImplement($driver, $tablepre, $class = null) {/*{{{*/
        return parent::getImplement($driver, $tablepre, __CLASS__);
    }/*}}}*/



    //------------------自定义函数-----------------


    //------------------示例函数-------------------
    function insertQATask($keyValues) {
        $pre = $this->tablepre;
        $table = "{$pre}qatask";
        return $this->wrapper->insert($table, $keyValues);
    }

}
