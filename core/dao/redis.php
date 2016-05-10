<?php
/**
 * phpredis
 * created by lane
 * @2014-05-28
 */
class RedisCache {
    private $cacheType = 'redis';
    private $redis;
    private $keyPre;

    public function __construct($keyPre='', $host='127.0.0.1', $port='6379', $authPwd = '') {
        $this->keyPre = $keyPre;

        //try to connect
        $this->redis = new Redis();
        $serverOk = $this->redis->connect($host, $port);
        if (!$serverOk) {
            die('Error: redis connect failed.');
        }

        if ($authPwd) {     //auth with password
            $this->redis->auth($authPwd);
        }
    }

    public function type() {
        return $this->cacheType;
    }

    public function keys($reg = '*') {
        return $this->redis->keys($this->keyPre . $reg);
    }
    
    public function del($key) {
        return $this->redis->del($this->keyPre . $key);
    }

    public function get($key) {
        $val = $this->redis->get($this->keyPre . $key);
        if (!empty($val) && is_string($val) && preg_match('/^\w+:\d+.*/i', $val)) {
            try {
                $out = unserialize($val);
            } catch (Exception $e) {
                $out = $val;
            }
            return $out;
        }else {
            return $val;
        }
    }

    public function exists($key) {
        return $this->redis->exists($this->keyPre . $key);
    }

    /**
     * default never expire
     */
    public function set($key, $val, $expire = 0) {
        if (!is_string($val) && !is_numeric($val)) {
            $val = serialize($val);
        }

        if ($expire > 0) {
            return $this->redis->set($this->keyPre . $key, $val, $expire);
        }else {
            return $this->redis->set($this->keyPre . $key, $val);
        }
    }

    public function close() {
        $this->redis->close();
    }

    /**
     * sets
     * default expire is 30 minutes
	 * {{{
     */
    public function sAdd($key, $val, $expire = 1800) {
		$key = $this->keyPre . $key;
        $b = $this->redis->sAdd($key, $val);
        if ($b && $expire > 0) {
            $this->redis->expire($key, $expire);
        }
		return $b;
    }

    //判断某个成员是否在一个集合中存在
    public function sIsMember($key, $member) {
        $key = $this->keyPre . $key;
        return $this->redis->sIsMember($key, $member);
    }

    public function sMembers($key) {
		$key = $this->keyPre . $key;
		return $this->redis->sMembers($key);
    }

    //随机取指定个数集合成员，兼容低版本redis扩展
    public function sRandMember($key, $num = 10) {
        $key = $this->keyPre . $key;
        $res = $this->redis->sRandMember($key, $num);
        if (!$res) {
            $mem = $this->redis->sRandMember($key);
            $res = $mem ? array($mem) : array();
        }
        return $res;
    }
    
    public function sRemove($key, $member) {
		$key = $this->keyPre . $key;
		return $this->redis->sRemove($key, $member);
    }

	//有序集合
    public function zAdd($key, $score, $val, $expire = 1800) {
		$key = $this->keyPre . $key;
        $b = 0;
		if (!$this->redis->exists($key)) {
            $b = $this->redis->zAdd($key, $score, $val);
            if ($expire > 0) {
                $this->redis->expire($key, $expire);
            }
        }else {
            $b = $this->redis->zAdd($key, $score, $val);
        }
		return $b;
    }

	//获取集合中指定值的分数（排序）
    public function zScore($key, $val) {
		$key = $this->keyPre . $key;
		return $this->redis->zScore($key, $val);
    }

    public function zRange($key, $start = 0, $end = -1, $withscores = false) {
		$key = $this->keyPre . $key;
		return $this->redis->zRange($key, $start, $end, $withscores);
    }

    public function zRangeByScore($key, $start = 0, $end =100, $option=array()) {
        $key = $this->keyPre . $key;
        return $this->redis->zRangeByScore($key, $start, $end, $option);
    }

    public function zRevRange($key, $start = 0, $end = -1, $withscores = false) {
		$key = $this->keyPre . $key;
		return $this->redis->zRevRange($key, $start, $end, $withscores);
    }

    public function zCard($key) {
		$key = $this->keyPre . $key;
        return $this->redis->zCard($key);
    }

    public function zCount($key, $start = '0', $end = '100') {
		$key = $this->keyPre . $key;
        return $this->redis->zCount($key, $start, $end);
    }

    public function zRank($key, $val) {
		$key = $this->keyPre . $key;
        return $this->redis->zRank($key, $val);
    }

    public function zRevRank($key, $val) {
		$key = $this->keyPre . $key;
        return $this->redis->zRevRank($key, $val);
    }
    
    public function zDelete($key, $val) {
		$key = $this->keyPre . $key;
        return $this->redis->zDelete($key, $val);
    }

    //hashes
    public function hSet($key, $field, $val) {
		$key = $this->keyPre . $key;
        return $this->redis->hSet($key, $field, $val);
    }

    public function hGet($key, $field) {
		$key = $this->keyPre . $key;
        return $this->redis->hGet($key, $field);
    }
    
    public function hDel($key) {
		$key = $this->keyPre . $key;
        return $this->redis->hDel($key);
    }

    public function hGetAll($key) {
        $key = $this->keyPre . $key;
        return $this->redis->hGetAll($key);
    }

    public function hMSet($key, $arr) {
        $key = $this->keyPre . $key;
        return $this->redis->hMSet($key, $arr);
    }

    public function hMGet($key, $arr) {
        $key = $this->keyPre . $key;
        return $this->redis->hMGet($key, $arr);
    }

    //list
    public function lPush($key, $val) {
		$key = $this->keyPre . $key;
        return $this->redis->lPush($key, $val);
    }

    public function rPush($key, $val) {
        $key = $this->keyPre . $key;
        return $this->redis->rPush($key, $val);
    }

    public function lPop($key) {
		$key = $this->keyPre . $key;
        return $this->redis->lPop($key);
    }

    public function rPop($key) {
		$key = $this->keyPre . $key;
        return $this->redis->rPop($key);
    }
    
    public function lRemove($key, $val, $count = 0) {
		$key = $this->keyPre . $key;
        return $this->redis->lRemove($key, $val, $count);
    }

    public function lRange($key, $start, $end) {
        $key = $this->keyPre . $key;
        return $this->redis->lRange($key, $start, $end);
    }

    public function incr($key){
        $key = $this->keyPre . $key;
        return $this->redis->incr($key);
    }
    /**
	 * }}}
     */
}
