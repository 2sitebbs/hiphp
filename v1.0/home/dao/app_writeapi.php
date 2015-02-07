<?php
/*
 * write dao implement
 * by lane @2014-05-08
 * -------------------
*/

class AppWriteApis extends DAOWriteApis {

    //implement the abstract function
    public static function getImplement($driver, $tablepre, $class = null) {/*{{{*/
        return parent::getImplement($driver, $tablepre, __CLASS__);
    }/*}}}*/



    //------------------自定义函数-----------------

}
