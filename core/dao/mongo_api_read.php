<?php
/*
 * mongodb read dao implement
 * by lane @2013-08-21
 * -------------------
 * count, and, or support @2014-04-24
 * 添加常用查询条件支持，格式如：array('field1' => 'value1', 'field2' => '> value2', 'field3' => '< value3', 'field3' => '!= value3')
*/
require_once 'mongo_wrapper.php';
require_once 'mongo_implements.php';

class MongoDAOReadApis extends MongoDAOImplement {
    //protected static $impl;     //用于保存实例对象的数组

    /* 动态函数调用 */
    function __call($method, $arguments) {/*{{{*/
        $arr = $this->parseMethod($method);
        if (!empty($arr) && count($arr) >= 2) {
            $table = strtolower($arr[1]);
            $field = isset($arr[2]) ? $arr[2] : '';

            //deal with specialchars
            Util::htmlspecialchars($arguments);

            return $this->todo($table, $field, $arguments);
        }else {
            return null;
        }
    }/*}}}*/

    //implement the abstract function
    public static function getImplement($driver, $tablepre, $class = null){/*{{{*/
        $key = 'daoread_' . $driver[DBHOST] . $driver[DBUSER];
        if (!isset(self::$impl[$key])) {
            $class = $class ? $class : __CLASS__;
            self::$impl[$key] = new $class($driver, $tablepre);
        }
        return self::$impl[$key];
    }/*}}}*/

    //解析方法名，得到操作的表名和字段名
    protected function parseMethod($method) {
        $reg = '/^get(\w+)(?:By(\w+))?$/iU';
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

            //增加in查询
            'in' => '$in',
        );

        return isset($ops[$op]) ? $ops[$op] : '';
    }

    //为mongodb解析查询字段
    protected function getQueryFields4Mongodb($fields = '*') {
        $arrField = array();
        if ($fields == '*' || empty($fields)) {
            return $arrField;
        }else {
            $arr = explode(',', $fields);
            if (!empty($arr)) {
                foreach ($arr as $field) {
                    $arrField[$field] = 1;
                }
            }else {
                $arrField[$fields] = 1;
            }
        }

        return $arrField;
    }

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
            $out['op'] = $this->getMongodbOperator($match[1]);
            $out['value'] = $match[2] == '""' || $match[2] == "''" ? '' : Util::strtonumber($match[2]);
        }else {
            $out['op'] = '';
            $out['value'] = Util::strtonumber($value);
        }

        return $out;
    }

    //默认的数据读取函数，如需特殊处理另行定义
    protected function todo($tableName, $field = '', $args = array()) {
        $pre = $this->tablepre;
        $table = "{$pre}{$tableName}";
        $fields = '*';

        //count support
        $reg = '/^count(\w+)$/iU';
        preg_match($reg, $tableName, $match);
        $count = false;
        if (!empty($match)) {
            $table = "{$pre}{$match[1]}";
            //$fields = 'count(0) as total';
            $count = true;
        }

        $id = null;   //默认无传值
        $condition = array();    //默认查询所有数据

        if (!empty($field)) {  //如果指定查询字段
            $id = isset($args[0]) ? $args[0] : null;    //所有读取数据的动作默认接收一个参数
            if (is_null($id)) {     //如果指定字段查询，但未传参数，或传参null，则返回null
                return null;
            }

            $order = isset($args[1]) ? $args[1] : '';   //第二个参数为order by
            $limit = isset($args[2]) ? $args[2] : 0;   //第三个参数为limit

            //默认只指定一个字段进行查询
            $out = $this->parseOperator($id);
            if (!empty($out['op'])) {
                $condition[strtolower($field)][$out['op']] = $out['value'];
            }else {     //等于查询
                $condition[strtolower($field)] = $out['value'];
            }

            //and, or support，如果指定了两个字段
            $regf = '/^(\w+)(And|Or)(\w+)$/U';
            preg_match($regf, $field, $matchf);
            if (!empty($matchf)) {
                list(, $field1, $op, $field2) = $matchf;

                $out = $this->parseOperator($id);
                $condition = array();       //清空条件
                if (!empty($out['op'])) {
                    $condition[$field1][$out['op']] = $out['value'];
                }else {     //等于查询
                    $condition[$field1] = $out['value'];
                }

                if (isset($args[1])) {
                    $out = $this->parseOperator($args[1]);
                    if (!empty($out['op'])) {
                        $condition[$field2][$out['op']] = $out['value'];
                    }else {     //等于查询
                        $condition[$field2] = $out['value'];
                    }

                    $order = isset($args[2]) ? $args[2] : '';   //第三个参数为order by
                    $limit = isset($args[3]) ? $args[3] : 0;   //第四个参数为limit
                }else {       //如果没传第二个参数，则认为两个字段都使用同一个值
                    $out = $this->parseOperator($args[0]);
                    if (!empty($out['op'])) {
                        $condition[$field2][$out['op']] = $out['value'];
                    }else {     //等于查询
                        $condition[$field2] = $out['value'];
                    }
                }

                //补上and和or逻辑判断
                $newCondition = array();
                foreach ($condition as $key => $val) {
                    $newCondition[] = array(strtolower($key) => $val);
                }
                $condition = array(
                    $this->getMongodbOperator(strtolower($op)) => $newCondition,
                );
            }
        }else {     //如果不指定查询字段，第一个参数传所有条件的数组
            /**如果第一个参数传参为数组，则认为是添加查询条件
             * 格式如：array('field1' => 'value1', 'field2' => '> value2', 'field3' => '< value3', 'field3' => '!= value3')
             * 增加同一字段多条件支持，格式如：array('field1' => '>= 1', 'field2' => array('>= 1', '<= 20'))
             * 增加同一字段多条件支持，格式如：array('field1' => '>= 1', 'field2' => array('>=' => 1, '<=' => 20))
             * 增加in, all查询支持，格式如：array('field1' => '>= 1', 'field2' => array('in' => array(1, 2)))
             * 增加or支持，格式如：array('field1' => '>= 1', 'field2' => array('or' => array(1,2)))
             * 增加or支持，格式如：array('field1' => '>= 1', 'or' => array('field1' => 1, 'field2' => 2))
             */
            if (isset($args[0]) && is_array($args[0])) {
                foreach ($args[0] as $field => $val) {
                    //增加多字段or查询支持
                    if ($field == 'or') {
                        $condition['$or'] = array();
                        foreach ($val as $fd => $item) {
                            $condition['$or'][] = array($fd => $item);
                        }
                    }else if(is_array($val)){     //如果字段的查询条件为数组
                        //增加in和all查询支持 @2016-04-26
                        foreach ($val as $key => $value) {
                            $op = $this->getMongodbOperator($key);  //优先解析以key传参操作符的方式
                            if ($op) {
                                if ($op != '$or') {
                                    $condition[$field][$op] = $value;
                                    continue;
                                }else {
                                    //增加or查询支持
                                    $condition[$op] = array();
                                    foreach ($value as $item) {
                                        $condition[$op][] = array($field => $item);
                                    }
                                    continue;
                                }
                            }

                            //再尝试以表达式方式解析
                            $out = $this->parseOperator($value);
                            if (!empty($out['op'])) {
                                $condition[$field][$out['op']] = $out['value'];
                            }else {     //等于查询
                                $condition[$field] = $out['value'];
                            }
                        }
                    }else{      //如果字段查询条件非数组
                        $out = $this->parseOperator($val);
                        if (!empty($out['op'])) {
                            $condition[$field][$out['op']] = $out['value'];
                        }else {     //等于查询
                            $condition[$field] = $out['value'];
                        }
                    }
                }

                $order = isset($args[1]) ? $args[1] : '';   //第二个参数为order by
                $limit = isset($args[2]) ? $args[2] : 0;   //第三个参数为limit
            }else { //如果第一个参数不是条件数组，则认为是orderby排序参数
                $order = isset($args[0]) ? $args[0] : '';   //第一个参数为order by
                $limit = isset($args[1]) ? $args[1] : 0;   //第二个参数为limit
            }
        }

        $mongoFields = $this->getQueryFields4Mongodb($fields);      //查询字段转换

        //如果是count统计
        if ($count) {
            $total = $this->wrapper->count($table, $condition);

            //兼容mysql取数据的方式
            return array(
                array('total' => $total)
            );
        }

        return $this->wrapper->select($table, $mongoFields, $condition, $order, $limit);
    }

    /* read apis */
    /*function testGet() {
        $pre = $this->tablepre;
        $table = "{$pre}test";
        $fields = array();
        $condition = array(
            'username' => 'test',
        );
        return $this->wrapper->select($table, $fields, $condition);
    }*/

}
