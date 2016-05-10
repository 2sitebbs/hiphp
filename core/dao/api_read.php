<?php
/*
 * read dao implement
 * by lane @2013-08-21
 * -------------------
 * count, and, or support @2014-04-24
 * 添加常用查询条件支持，格式如：array('field1' => 'value1', 'field2' => '> value2', 'field3' => '< value3', 'field3' => '!= value3')
*/
require_once 'wrapper.php';
require_once 'implements.php';

class DAOReadApis extends DAOImplement {
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
            $fields = 'count(0) as total';
            $count = true;
        }

        $id = null;   //默认无传值
        $condition = '';    //默认查询所有数据

        if (!empty($field)) {  //如果指定查询字段
            $id = isset($args[0]) ? $args[0] : null;    //所有读取数据的动作默认接收一个参数
            if (is_null($id)) {     //如果指定字段查询，但未传参数，或传参null，则返回null
                return null;
            }

            $order = isset($args[1]) ? $args[1] : '';   //第二个参数为order by
            $limit = isset($args[2]) ? $args[2] : '';   //第三个参数为limit

            $out = $this->parseOperator($id);
            $condition = strtolower($field) . " {$out['op']} '{$out['value']}'";

            //and, or support
            $regf = '/^(\w+)(And|Or)(\w+)$/U';
            preg_match($regf, $field, $matchf);
            if (!empty($matchf)) {
                list(, $field1, $op, $field2) = $matchf;

                $out = $this->parseOperator($id);
                $condition = strtolower($field1) . " {$out['op']} '{$out['value']}'";

                if (isset($args[1])) {
                    $out = $this->parseOperator($args[1]);
                    $condition .= " {$op} " . strtolower($field2) . " {$out['op']} '{$out['value']}'";

                    $order = isset($args[2]) ? $args[2] : '';   //第三个参数为order by
                    $limit = isset($args[3]) ? $args[3] : '';   //第四个参数为limit
                }else {       //如果没传第二个参数，则认为两个字段都使用同一个值
                    $out = $this->parseOperator($args[0]);
                    $condition .= " {$op} " . strtolower($field2) . " {$out['op']} '{$out['value']}'";
                }
            }
        }else {     //如果不指定查询字段
            /**如果第一个参数传参为数组，则认为是添加查询条件
             * 格式如：array('field1' => 'value1', 'field2' => '> value2', 'field3' => '< value3', 'field3' => '!= value3')
             */
            if (isset($args[0]) && is_array($args[0])) {
                foreach ($args[0] as $field => $val) {
                    $out = $this->parseOperator($val);
                    $condition .= !empty($condition) ? " and {$field} {$out['op']} '{$out['value']}'" : "{$field} {$out['op']} '{$out['value']}'";
                }
                $order = isset($args[1]) ? $args[1] : '';   //第二个参数为order by
                $limit = isset($args[2]) ? $args[2] : '';   //第三个参数为limit
            }else {
                $order = isset($args[0]) ? $args[0] : '';   //第一个参数为order by
                $limit = isset($args[1]) ? $args[1] : '';   //第二个参数为limit
            }
        }

        return $this->wrapper->select($table, $fields, $condition, $order, $limit);
    }

    /* read apis for test */
    /*function getQAsByTaskId($taskId) {
        $pre = $this->tablepre;
        $table = "{$pre}question q,{$pre}qtype qt,{$pre}answer a";
        $fields = 'q.*,qt.qtype,a.aid,a.answer';
        $condition = "q.tid='{$taskId}' and a.qid=q.qid and qt.qtid=q.qtid";
        $order = 'q.qid,a.aid';
        return $this->wrapper->select($table, $fields, $condition, $order);
    }

    function getResultsByTaskId($taskId, $stats = false) {
        $pre = $this->tablepre;
        $table = "{$pre}taskresult tr,{$pre}useranswer ua,{$pre}question q,{$pre}answer a";
        $fields = 'q.*,tr.addtime,ua.*,a.answer';
        $condition = "tr.tid='{$taskId}' and ua.uid=tr.uid and q.qid=ua.qid and a.aid=ua.aid";
        $order = '';

        if ($stats) {   //查询统计数据
            $table = "({$pre}taskresult tr,{$pre}question q,{$pre}answer a) left join {$pre}useranswer ua
                    on ua.uid=tr.uid and ua.qid=q.qid and ua.aid=a.aid";
            $fields = 'q.qid,q.question,a.answer,count(ua.aid) as counter';
            $condition = "tr.tid='{$taskId}' and q.tid=tr.tid and a.qid=q.qid";
            $condition .= " group by q.qid,a.aid";
            $order = 'q.qid,a.aid';
        }
        return $this->wrapper->select($table, $fields, $condition, $order);
    }*/

}
