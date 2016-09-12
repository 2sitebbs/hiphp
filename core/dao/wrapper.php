<?php
/*
 * dao wrapper with mysqli
 * include connect, close, select, insert, update, delete, truncate
 * by lane @2013-09-20
*/
require_once 'base.php';

class DAOWrapper extends Base {
    private static $wrapper;
    private $driver;
    private $link;
    private function __construct($driver) {
        $this->driver = $driver;
        $this->link = $this->connect($driver);
        if (!$this->link || !mysqli_set_charset($this->link, "utf8")) {
            $this->echoError("DB connection error: " . mysqli_connect_error());
        }
    }

    function __clone() {
        return self::$wrapper;
    }

    function __toString() {
        return "DAO wrapper using connection {$this->link}.";
    }

    public static function getWrapper($driver, $new = false) {
        if (empty(self::$wrapper)) {
            self::$wrapper = new DAOWrapper($driver);
        }else if ($new) {
            return new DAOWrapper($driver);
        }
        return self::$wrapper;
    }

    function connect($driver) {
        $host = $driver[DBHOST];
        $user = $driver[DBUSER];
        $password = $driver[DBPASSWORD];
        $database = $driver[DATABASE];
        $dbport = isset($driver[DBPORT]) ? $driver[DBPORT] : '3306';

        return mysqli_connect($host, $user, $password, $database, $dbport);
    }

    function close() {
        return mysqli_close($this->link);
    }

    function select($table, $fields, $condition = '', $order = '', $limit = '') {
        $sql = "select $fields from $table" .
            //support only group by in condition
            (!empty($condition) ? (preg_match('/^group by .+$/i', $condition) ? " {$condition}" : " where {$condition}") : "") .
            (!empty($order) ? " order by $order" : "") .
            (!empty($limit) ? " limit $limit" : "");
        $rs = mysqli_query($this->link, $sql);
        return $this->getArray($rs);
    }

    //转义之前判断下是否有需要转义的字符
    private function htmlspecialchars($str) {
        $trytodecode = htmlspecialchars_decode($str, ENT_QUOTES);

        //如果没有被转义，则将其转义
        if ($trytodecode == $str) {
            return htmlspecialchars($str, ENT_QUOTES);
        }

        //如果已经转义，则不再转义
        return $str;
    }
    
    //注意，如果是联合索引请不要使用第三个参数，mysql只会更新符合条件的第一条数据，而不是所有行
    //增加主键冲突时更新操作可指定操作符，参数格式：array('field1' => '+')
    function insert($table, $keyValues = array(), $duplicateUpdates = array(), $duplicateOption = array()) {
        $fields = '';
        $values = '';
        foreach ($keyValues as $key => $val) {
            $fields .= "{$key},";
            $values .= "'" . $this->htmlspecialchars($val, ENT_QUOTES) . "',";
        }
        $fields = preg_replace('/,$/', '', $fields);
        $values = preg_replace('/,$/', '', $values);

        $sql = "insert into {$table}({$fields}) values({$values})";

        if (!empty($duplicateUpdates) && is_array($duplicateUpdates)) {
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            foreach ($duplicateUpdates as $k) {
                if (isset($keyValues[$k])) {
                    $sql .= !$duplicateOption ? "{$k}='{$keyValues[$k]}'," : "{$k}={$k}{$duplicateOption[$k]}'{$keyValues[$k]}',";
                }
            }
            $sql = preg_replace('/,$/', '', $sql);
        }

        //反斜杠处理
        $sql = str_replace('\\', '\\\\', $sql);

        return mysqli_query($this->link, $sql);
    }

    //获取最新插入数据的主键值
    function insert_id() {
        return mysqli_insert_id($this->link);
    }

    //增加指定操作符进行更新
    //keyValues传值格式：array('field' => array('+' => 10))
    function update($table, $keyValues = array(), $condition = '', $limit = 0) {
        $sql = "update $table set ";
        foreach ((array)$keyValues as $key => $value) {
            if (!is_array($value)) {
                $sql .= "{$key}='" . $this->htmlspecialchars($value, ENT_QUOTES) . "',";
            }else {
                $ops = array_keys($value);
                $sql .= "{$key}={$key}{$ops[0]}'" . $this->htmlspecialchars($value[$ops[0]], ENT_QUOTES) . "',";
            }
        }
        $sql = preg_replace('/,$/', '', $sql);
        $sql .= (!empty($condition) ? " where $condition" : "") . ($limit > 0 ? " limit $limit" : "");

        //反斜杠处理
        $sql = str_replace('\\', '\\\\', $sql);

        return mysqli_query($this->link, $sql) && mysqli_affected_rows($this->link);
    }

    function del($table, $condition = '', $limit = 0) {
        $sql = "delete from $table" .
            (!empty($condition) ? " where $condition" : "") .
            ($limit > 0 ? " limit $limit" : "");
        return mysqli_query($this->link, $sql) && mysqli_affected_rows($this->link);
    }

    function truncate($table) {
        $sql = "truncate $table";
        return mysqli_query($this->link, $sql);
    }

    function query($sql) {
        return mysqli_query($this->link, $sql);
    }

    function select_db($dbname) {
        return mysqli_select_db ($this->link, $dbname);
    }

    /* get array data from  mysqli_result result */
    function getArray($rs, $field = '') {
        $rows = array();
        if (!$rs) {
            return $rows;
        }

        //go first row and first field
        mysqli_data_seek($rs, 0);
        mysqli_field_seek($rs, 0);

        //just with field names
        while ($row = mysqli_fetch_array($rs, MYSQLI_ASSOC)) {
            if (empty($field)) {
                $rows[] = $row;
            }else {
                $rows[] = $row[$field];
            }
        }

        return $rows;
    }
}
