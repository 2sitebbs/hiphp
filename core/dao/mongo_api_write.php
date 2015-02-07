<?php
/*
 * mongodb add dao implement
 * by lane @2014-11-17
 * -------------------
 * add and support @2014-05-10
*/
require_once 'mongo_wrapper.php';
require_once 'mongo_implements.php';

class MongoDAOWriteApis extends MongoDAOImplement {
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

    //替换普通操作符为mongodb的操作符
    protected function getMongodbOperator($op) {
        $ops = array(
            '=' => '',
            '>' => '$gt',
            '>=' => '$gte',
            '<' => '$lt',
            '<=' => '$lte',
            '!=' => '$ne',

            //逻辑操作符
            'and' => '$and',
            'or' => '$or',
        );

        return isset($ops[$op]) ? $ops[$op] : '';
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
                $result = $this->wrapper->insert($table, $arrKeyValues);
                break;
            case 'update':
                if (count($args) < 2) {
                    return null;
                }

                $condition = array();

                $id = $args[0];
                $arrKeyValues = $args[1];   //第二个参数为数据数组
                $idName = !empty($field) ? strtolower($field) : '_id';   //默认使用id主键
                //$condition = "{$idName}='{$id}'";
                $condition[strtolower($idName)] = $id;

                //and support
                $regf = '/^(\w+)(And|Or)(\w+)$/U';
                preg_match($regf, $field, $matchf);
                if (!empty($matchf)) {
                    list(, $field1, $op, $field2) = $matchf;
                    //$condition = strtolower($field1) . "='{$id}'";
                    $condition = array();
                    $condition[strtolower($field1)] = $id;

                    if (isset($args[1])) {
                        //$condition .= " {$op} " . strtolower($field2) . "='{$args[1]}'";
                        $condition[strtolower($field2)] = $args[1];
                        $arrKeyValues = $args[2];   //第三个参数为数据数组

                        //补上and和or逻辑判断
                        $newCondition = array();
                        foreach ($condition as $key => $val) {
                            $newCondition[] = array(strtolower($key) => $val);
                        }
                        $condition = array(
                            $this->getMongodbOperator(strtolower($op)) => $newCondition,
                        );
                    }
                }

                $result = $this->wrapper->update($table, $arrKeyValues, $condition);
                break;
            case 'delete':
                $id = $args[0];   //所有删除数据的动作默认只接收一个参数，且为id唯一编号
                $idName = !empty($field) ? strtolower($field) : '_id';   //默认使用id主键
                //$condition = "{$idName}='{$id}'";

                $condition = array();
                $condition[strtolower($idName)] = $id;

                //and support
                $regf = '/^(\w+)(And|Or)(\w+)$/U';
                preg_match($regf, $field, $matchf);
                if (!empty($matchf)) {
                    list(, $field1, $op, $field2) = $matchf;
                    //$condition = strtolower($field1) . "='{$id}'";
                    $condition = array();
                    $condition[strtolower($field1)] = $id;

                    if (isset($args[1])) {
                        //$condition .= " {$op} " . strtolower($field2) . "='{$args[1]}'";
                        $condition[strtolower($field2)] = $args[1];

                        //补上and和or逻辑判断
                        $newCondition = array();
                        foreach ($condition as $key => $val) {
                            $newCondition[] = array(strtolower($key) => $val);
                        }
                        $condition = array(
                            $this->getMongodbOperator(strtolower($op)) => $newCondition,
                        );
                    }
                }

                $result = $this->wrapper->del($table, $condition);
                break;
        }

        return $result;
    }

    public function insert_id($table) {
        return $this->wrapper->insert_id($this->tablepre . $table);
    }

}
