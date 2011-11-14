<?php
namespace Sleek;

class Cache_File implements Cache_Base {
    /**
     * @var string
     */
    protected $directory            = NULL;

    /**
     * @var int
     */
    protected $expireTime           = 0;

    /**
     * @param int $expireTime
     * @param string $directory
     */
    public function __construct($expireTime, $directory) {
        $this->directory            = $directory;
        $this->expireTime           = (int) $expireTime;
    }

    /**
     * Stores a key/value
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Gets a cached value
     * @param string $key
     * @return mixed|null
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Stores a key/value
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value) {
        $filename = $this->getFileName($key);
        $dataSerialized = serialize($value);
        $fh = fopen($filename, 'w');
        if (!$fh) {
            return FALSE;
        }
        fwrite($fh, $dataSerialized);
        fclose($fh);
        return TRUE;
    }

    /**
     * Gets a cached value
     * @param string $key
     * @return mixed|null
     */
    public function get($key) {
        $filename = $this->getFileName($key);
        if (!file_exists($filename)) {
            return NULL;
        }
        $data = file_get_contents($filename);
        if (!$data) {
            return NULL;
        }
        return unserialize($data);
    }

    /**
     * Replaces an existing value with another one
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function replace($key, $value) {
        return $this->set($key, $value);
    }

    /**
     * Deletes a stored value
     * @param string $key
     * @return bool
     */
    public function delete($key) {
        $filename = $this->getFileName($key);
        if (!file_exists($filename)) {
            return FALSE;
        }
        return @unlink($filename);
    }

    /**
     * Gets a filename to the cache file based on the key name
     * @param string $key
     * @return string
     */
    private function getFileName($key) {
        $key = (string) $key;
        return $this->directory . md5($key) . '.cache';
    }
}
