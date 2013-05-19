<?php
namespace Sleek;

class Cache {
    /**
     * @var \Sleek\Cache
     */
    protected static $_instance         = NULL;

    private function __construct() {}

    private function __clone() {}

    /**
     * Returns a new cache (doesn't need to be the one defined in the config file). Cache doesn't keep a copy of this instance.
     * @param $type string
     * @return Cache_APC|Cache_File|Cache_Memcache
     */
    public static function factory($type) {
        return self::buildCache($type);
    }

    /**
     * Returns the "Default" cache, described in the config file
     * @return Cache_APC|Cache_File|Cache_Memcache
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = self::buildCache(Config::get('cache_method'));
        }
        return self::$_instance;
    }

    /**
     * Factory method for creating new caches. Returns a cache of the type specified.
     * Uses configuration from APP_PATH/config.ini
     * @static
     * @param string $type
     * @return null|Cache_APC|Cache_File|Cache_Memcache
     */
    protected static function buildCache($type) {
        switch($type) {
            case 'memcache':
                return new Cache_Memcache(Config::get('cache_expiretime'), Config::get('cache_memcache_servers'));
                break;
            case 'apc':
                return new Cache_APC(Config::get('cache_expiretime'));
                break;
            case 'file':
                return  new Cache_File(Config::get('cache_expiretime'), Config::get('cache_file_directory'));
                break;
            default:
                return NULL;
                break;
        }
    }
}
