<?php
namespace Sleek;

class Cache_APC implements Cache_Base {
    /**
     * The time a cached value is good for, 0 = forever
     * @var int
     */
    protected $expireTime = 0;

    /**
     * @param int $expireTime
     */
    public function __construct($expireTime) {
        $this->expireTime = (int) $expireTime;
    }

    /**
     * Sets a key value
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Gets a value with the provided key
     * @param string $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Sets a key value
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value) {
        return apc_store($key, $value, $this->expireTime);
    }

    /**
     * Gets a value with the provided key
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return apc_fetch($key);
    }

    /**
     * Replaces an existing value with another one for the provided key
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function replace($key, $value) {
        apc_delete($key);
        return apc_store($key, $value, $this->expireTime);
    }

    /**
     * Deletes a cached value with the provided key
     * @param string $key
     * @return bool
     */
    public function delete($key) {
        return apc_delete($key);
    }
}
