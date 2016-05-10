<?php
/*
 * dao wrapper with mysqli
 * include connect, close, select, insert, update, delete, truncate
 * by lane @2013-09-20
*/
require_once 'base.php';

class MongoDAOWrapper extends Base {
    private static $wrapper;
    private $driver;
    private $database;
    private $mongo;
    private $link;

    //保存表中插入的最新_id值
    private $_ids;

    private function __construct($driver) {
        $this->driver = $driver;

        $this->_ids = array();     //初始化自增字段保存数组

        $host = $driver[DBHOST];
        $user = $driver[DBUSER];
        $password = $driver[DBPASSWORD];
        $database = $driver[DATABASE];
        $this->database = $database;
        $dbport = isset($driver[DBPORT]) ? $driver[DBPORT] : '27017';

        $this->mongo = new MongoClient("mongodb://{$host}", array(
                'username' => $user,
                'password' => $password,
                'db' => $database,
                'connect' => false,
                //'connectTimeoutMS' => 5000,
                //'socketTimeoutMS' => 5000,
            ));
    }

    function __destruct() {
        $this->close();
    }

    function __clone() {
        return self::$wrapper;
    }

    function __toString() {
        return "DAO wrapper for mongodb.";
    }

    public static function getWrapper($driver, $new = false) {
        if (empty(self::$wrapper)) {
            self::$wrapper = new MongoDAOWrapper($driver);
        }else if ($new) {
            return new MongoDAOWrapper($driver);
        }
        return self::$wrapper;
    }

    function connect($collection) {
        $database = $this->database;
        if ($this->link) {
            $this->link = null;
        }
        $this->link = $this->mongo->selectDB($database)->selectCollection($collection);
        return $this->link;
    }

    function select_db($db) {
        $this->database = $db;
        return $this->mongo->selectDB($db);
    }

    function getConnections() {
        return $this->mongo->getConnections();
    }

    function close() {
        $connections = $this->mongo->getConnections();
        foreach ( $connections as $con ) {
            // Loop over all the connections, and when the type is "SECONDARY"
            // we close the connection
            try{
                $this->mongo->close( $con['hash'] );
            }catch(Exception $e){}
        }
    }

    function select($table, $fields, $condition = array(), $order = '', $limit = '') {
        $c = $this->connect($table);
        $rs = null;

        //兼容limit写法：0, 20
        $start = '';
        if (strpos($limit, ',') !== false) {
            $tarr = explode(',', $limit);
            if (isset($tarr[1])) {
                $start = trim($tarr[0]);
                $limit = trim($tarr[1]);
            }
        }

        /*
        //翻页实现
        if ($start !== '' && !isset($condition['_id']['$lt']) && !isset($condition['_id']['$gt']) && !isset($condition['_id']['$lte'])) {
            //采用_id判断来代替skip
            if (!isset($condition['_id'])) {
                $condition['_id'] = array('$gte' => intval($start));
            }else {
                $condition['_id']['$gte'] = intval($start);
            }
        }
        */


        //查询数据
        $condition = Util::arraytonumber($condition);
        $rs = $c->find($condition, $fields);

        //使用skip实现翻页
        if ($start !== '') {
            $rs->skip($start);
        }

        //限制数量
        if (!empty($limit)) {
            $rs->limit($limit);
        }

        //排序
        if (!empty($order)) {
            if (!is_array($order)) {    //support string like: orderby desc
                if (strpos($order, ' desc') === false) {
                    $order = str_replace(' asc', '', strtolower($order));
                    if (strpos($order, ',') === false) {
                        $order = array($order => 1);
                    }else {
                        $arr = preg_split('/,/', $order);
                        $order = array();
                        foreach ($arr as $val) {
                            $order[$val] = 1;
                        }
                    }
                }else {
                    $order = str_replace(' desc', '', strtolower($order));
                    if (strpos($order, ',') === false) {
                        $order = array($order => -1);
                    }else {
                        $arr = preg_split('/,/', $order);
                        $order = array();
                        foreach ($arr as $val) {
                            $order[$val] = -1;
                        }
                    }
                }
            }
            $rs->sort($order);
        }

        $rs->timeout(-1);   //no timeout
        return $this->getArray($rs);
    }

    function insert($table, $keyValues = array()) {
        if (!isset($keyValues['_id'])) {    //设置自增id
            $keyValues['_id'] = $this->autoIncrementId("autoId_{$table}");
        }
        try {
            $c = $this->connect($table);
            $keyValues = Util::arraytonumber($keyValues);
            $res = $c->insert($keyValues);
            $this->_ids["autoId_{$table}"] = $keyValues['_id'];
            return $res;
        }catch(Exception $e) {
            return false;
        }
    }

    //获取自增字段最新值
    function insert_id($table) {
        return isset($this->_ids["autoId_{$table}"]) ? $this->_ids["autoId_{$table}"] : 0;
    }

    function update($table, $keyValues = array(), $condition = array(), $limit = 0) {
        try {
            $c = $this->connect($table);
            $keyValues = Util::arraytonumber($keyValues);
            $condition = Util::arraytonumber($condition);
            return $c->update($condition, array('$set' => $keyValues), array('multiple' => true));
        }catch(Exception $e) {
            return false;
        }   
    }

    function del($table, $condition = '', $limit = 0) {
        try {
            $c = $this->connect($table);
            $condition = Util::arraytonumber($condition);
            return $c->remove($condition);
        }catch(Exception $e) {
            return false;
        }
    }

    function count($table, $condition = '') {
        try {
            $c = $this->connect($table);
            $condition = Util::arraytonumber($condition);
            return $c->count($condition);
        }catch(Exception $e) {
            return -1;
        }
    }

    function truncate($table) {
        try {
            $c = $this->connect($table);
            return $c->remove();
        }catch(Exception $e) {
            return false;
        }
    }

    function drop($table) {
        $c = $this->connect($table);
        try {
            return $c->drop();
        }catch(MongoCursorException $e) {
            return false;
        }
    }

    function query($table, $condition = '') {
        return array();
    }

    function autoIncrementId($table = 'autoIncrementIds') {
        try {
            $c = $this->connect($table);
            $result = $c->findAndModify(
                array('_id' => 'autoIds'),
                array('$inc' => array('val' => 1)),
                null,
                array('new' => true, 'upsert' => true)
            );
        }catch(Exception $e) {
            return false;
        }

        if (isset($result['val'])) {
            return $result['val'];
        }

        throw new Exception('Mongo: gen auto increment id failed');
    }

    /* get array data from  mysqli_result result */
    function getArray($rs, $field = '') {
        $rows = array();
        if (!$rs) {
            return $rows;
        }

        //just with field names
        foreach ($rs as $row) {
            if (empty($field)) {
                $rows[] = $row;
            }else {
                $rows[] = $row[$field];
            }
        }

        return $rows;
    }


    //求和
    function sum($table, $groupField, $sumField, $match = array()) {
        try {
            $c = $this->connect($table);
            $op = array(
                array(
                    '$match' => $match
                ),
                array(
                    '$group' => array(
                        '_id' => array('uid' => "\${$groupField}"),
                        'total' => array('$sum' => "\${$sumField}")
                    )
                )
            );
            $rs = $c->aggregate($op);
            return $rs;
        }catch(Exception $e) {
            return false;
        }
    }


}