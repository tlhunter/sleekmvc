<?php
namespace Sleek;

class Cache_Memcache implements Cache_Base {
    /**
     * Array of available Memcache servers (strings as server:port)
     * @var array
     */
    protected $servers              = array();

    /**
     * Array of Memcache servers we're using (strings as server:port)
     * @var array
     */
    protected $connectedServers     = array();

    /**
     * Reference to the PHP Memcache class
     * @var \Memcache
     */
    protected $memcache             = NULL;

    /**
     * Default amount of time until expires (defaults to not expiring)
     * @var int
     */
    protected $expireTime           = 0;

    /**
     * @param int $expireTime
     * @param array $servers
     */
    function __construct($expireTime, $servers) {
        $this->memcache             = new \Memcache;
        $this->servers              = $servers;
        $this->expireTime           = (int) $expireTime;
    }

    /**
     * Attempts to connect to the available Memcache servers
     * @throws \Exception
     * @return void
     */
    protected function connect() {
        foreach ($this->servers as $server) {
            list($server, $port) = explode($server, ':');
            if ($this->memcache->addServer($server, $port)) {
                $this->connectedServers[] = $server;
            }
        }
        if (!$this->servers) {
            throw new \Exception;
        }
    }

    /**
     * Sets a value to the memcache servers
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Gets a value from the memcache servers
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Sets a value to the memcache servers
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value) {
        return $this->memcache->set($key, $value, 0, $this->expireTime);
    }

    /**
     * Gets a value from the memcache servers
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->memcache->get($key);
    }

    /**
     * Replaces a value in the memcache server
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function replace($key, $value) {
        return $this->memcache->replace($key, $value, 0, $this->expireTime);
    }

    /**
     * Deletes a value from the memcache server
     * @param string $key
     * @return bool
     */
    public function delete($key) {
        return $this->memcache->delete($key, 0);
    }

}
