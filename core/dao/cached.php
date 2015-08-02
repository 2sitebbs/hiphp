<?php
/**
 * cache use memcached
 * created by lane
 * @2015-01-05
 */
class MMCached {
    private $cacheType = 'memcached';
    private $mem;
    private $keyPre;

    public function __construct($keyPre = '', $host = '127.0.0.1', $port = 11211, $authUser = '', $authPwd = '') {
        $this->keyPre = $keyPre;

        //try to connect
        $this->mem = new Memcached;
        $this->mem->setOption(Memcached::OPT_COMPRESSION, false);       //关闭压缩功能
        $this->mem->setOption(Memcached::OPT_BINARY_PROTOCOL, true);    //使用binary二进制协议
        $serverOk = $this->mem->addServer($host, $port); 
        if (!$serverOk) {
            die('Error: memcached connect failed.');
        }

        if ($authUser && $authPwd) {     //auth with username and password
            $this->mem->setSaslAuthData($authUser, $authPwd);
        }
    }

    public function type() {
        return $this->cacheType;
    }

    public function addServer($host, $port = 11211) {
        return $this->mem->addServer($host, $port);
    }

    public function get($key) {
        return $this->mem->get($this->keyPre . $key);
    }

    /**
     * default expire is half of an hour
     */
    public function set($key, $val, $expire=600) {
        return $this->mem->set($this->keyPre . $key, $val, $expire);
    }

    //support auth
    public function setSaslAuthData($username, $password) {
        return $this->mem->setSaslAuthData($username, $password);
    }

    public function close() {
        return $this->mem->quit();
    }
}
