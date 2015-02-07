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
        if (!$this->link) {
            $this->echoError("DB connection error by {$driver[DBUSER]}: " . mysqli_connect_error());
        }
    }

    function __clone() {
        return self::$wrapper;
    }

    function __toString() {
        return "DAO wrapper using connection $this->link.";
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
        //error_log("SQL: {$sql}\n", 3, '/var/log/debug.log');
        return $this->getArray($rs);
    }

    //注意，如果是联合索引请不要使用第三个参数，mysql只会更新符合条件的第一条数据，而不是所有行
    function insert($table, $keyValues = array(), $duplicateUpdates = array()) {
        $fields = '';
        $values = '';
        foreach ($keyValues as $key => $val) {
            $fields .= "{$key},";
            $values .= "'{$val}',";
        }
        $fields = preg_replace('/,$/', '', $fields);
        $values = preg_replace('/,$/', '', $values);

        $sql = "insert into {$table}({$fields}) values({$values})";

        if (!empty($duplicateUpdates) && is_array($duplicateUpdates)) {
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            foreach ($duplicateUpdates as $k) {
                if (isset($keyValues[$k])) {
                    $sql .= "{$k}='{$keyValues[$k]}',";
                }
            }
            $sql = preg_replace('/,$/', '', $sql);
        }

        //反斜杠处理
        $sql = str_replace('\\', '\\\\', $sql);

        //error_log("Insert SQL: {$sql}\n", 3, '/var/log/debug.log');
        return mysqli_query($this->link, $sql);
    }

    //获取最新插入数据的主键值
    function insert_id() {
        return mysqli_insert_id($this->link);
    }

    function update($table, $keyValues = array(), $condition = '', $limit = 0) {
        $sql = "update $table set ";
        foreach ((array)$keyValues as $key => $value) {
            $sql .= "$key='{$value}',";
        }
        $sql = preg_replace('/,$/', '', $sql);
        $sql .= (!empty($condition) ? " where $condition" : "") . ($limit > 0 ? " limit $limit" : "");

        //反斜杠处理
        $sql = str_replace('\\', '\\\\', $sql);

        error_log("Update SQL: {$sql}\n", 3, '/var/log/debug.log');
        return mysqli_query($this->link, $sql);
    }

    function del($table, $condition = '', $limit = 0) {
        $sql = "delete from $table" .
            (!empty($condition) ? " where $condition" : "") .
            ($limit > 0 ? " limit $limit" : "");
        //error_log("Delete SQL: {$sql}\n", 3, '/var/log/debug.log');
        return mysqli_query($this->link, $sql);
    }

    function truncate($table) {
        $sql = "truncate $table";
        return mysqli_query($this->link, $sql);
    }

    function query($sql) {
        //error_log("Query SQL: {$sql}\n", 3, '/var/log/debug.log');
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
