<?php
/*
 * add dao implement
 * by lane @2013-08-22
 * -------------------
 * add and support @2014-05-10
*/
require_once 'wrapper.php';
require_once 'implements.php';

class DAOWriteApis extends DAOImplement {
    //protected static $impl;     //用于保存实例对象的数组

    /* 动态函数调用 */  
    function __call($method, $arguments) {/*{{{*/
        if (!method_exists($this, $method)) {
            $arr = $this->parseMethod($method);
            if (!empty($arr) && count($arr) >= 3) {
                $action = $arr[1];
                $table = strtolower($arr[2]);
                $field = isset($arr[3]) ? $arr[3] : '';

                //deal with specialchars
                Util::htmlspecialchars($arguments);

                return $this->todo($action, $table, $field, $arguments);
            }else {
                return null;
            }
        }
    }/*}}}*/

    //implement the abstract function
    public static function getImplement($driver, $tablepre, $class = null) {/*{{{*/
        $key = 'daowrite_' . $driver[DBHOST] . $driver[DBUSER];
        if (!isset(self::$impl[$key])) {
            $class = $class ? $class : __CLASS__;
            self::$impl[$key] = new $class($driver, $tablepre);
        }
        return self::$impl[$key];
    }/*}}}*/

    //解析方法名，得到操作的表名和字段名
    protected function parseMethod($method) {
        $reg = '/^(add|update|delete)(\w+)(?:By(\w+))?$/iU';
        preg_match($reg, $method, $match);

        return $match;
    }

    //默认的数据插入函数，如需特殊处理另行定义
    protected function todo($action, $tableName, $field = '', $args) {
        if (empty($args)) {
            return null;
        }

        $pre = $this->tablepre;
        $table = "{$pre}{$tableName}";

        switch ($action) {
            case 'add':
                $arrKeyValues = $args[0];   //所有插入数据的动作最多接收两个参数
                $arrDuplicateUpdateFields = isset($args[1]) ? $args[1] : array();   //第二个参数为设置是否忽略索引冲突的数据
                $result = $this->wrapper->insert($table, $arrKeyValues, $arrDuplicateUpdateFields);
                break;
            case 'update':
                if (count($args) < 2) {
                    return null;
                }

                $id = $args[0];
                $arrKeyValues = $args[1];   //第二个参数为数据数组
                $idName = !empty($field) ? strtolower($field) : 'id';   //默认使用id主键
                $condition = "{$idName}='{$id}'";

                //and support
                $regf = '/^(\w+)(And|Or)(\w+)$/U';
                preg_match($regf, $field, $matchf);
                if (!empty($matchf)) {
                    list(, $field1, $op, $field2) = $matchf;
                    $condition = strtolower($field1) . "='{$id}'";
                    if (isset($args[1])) {
                        $condition .= " {$op} " . strtolower($field2) . "='{$args[1]}'";
                        $arrKeyValues = $args[2];   //第三个参数为数据数组
                    }
                }

                $result = $this->wrapper->update($table, $arrKeyValues, $condition);
                break;
            case 'delete':
                $id = $args[0];   //所有删除数据的动作默认只接收一个参数，且为id唯一编号
                $idName = !empty($field) ? strtolower($field) : 'id';   //默认使用id主键
                $condition = "{$idName}='{$id}'";

                //and support
                $regf = '/^(\w+)(And|Or)(\w+)$/U';
                preg_match($regf, $field, $matchf);
                if (!empty($matchf)) {
                    list(, $field1, $op, $field2) = $matchf;
                    $condition = strtolower($field1) . "='{$id}'";
                    if (isset($args[1])) {
                        $condition .= " {$op} " . strtolower($field2) . "='{$args[1]}'";
                    }
                }

                $result = $this->wrapper->del($table, $condition);
                break;
        }

        return $result;
    }

    public function insert_id() {
        return $this->wrapper->insert_id();
    }

    public function query($sql) {
        return $this->wrapper->query($sql);
    }

}
