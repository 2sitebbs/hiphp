<?php
/**
 * cache use memcached
 * created by lane
 * @2012-08-30
 */
class MMCache {
    private $cacheType = 'memcache';
    private $mem;
    private $keyPre;

    public function __construct($keyPre='', $host='127.0.0.1', $port='11211') {
        $this->keyPre = $keyPre;

        //try to connect
        $this->mem = new Memcache;
        $serverOk = $this->mem->connect($host, $port);
        if (!$serverOk) {
            die('Error: memcache connect failed.');
        }
    }

    public function type() {
        return $this->cacheType;
    }

    public function get($key) {
        return $this->mem->get($this->keyPre . $key);
    }

    /**
     * default expire is half of an hour
     */
    public function set($key, $val, $expire=600) {
        return $this->mem->set($this->keyPre . $key, $val, MEMCACHE_COMPRESSED, $expire);
    }

    public function close() {
        return $this->mem->close();
    }
}
