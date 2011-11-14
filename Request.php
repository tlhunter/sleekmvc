<?php
namespace Sleek;

class Request {
    /**
     * @var Request The singleton instance of our request class
     */
    static private $_instance   = NULL;

    /**
     * @var array Array containing URL data
     */
    static protected $url      = array();

    /**
     * Initializes the Request singleton, sets data from $_GET variables
     */
    private function __construct() {
        self::$url['controller']   = (isset($_GET['controller']) ? ucfirst($_GET['controller']) : Config::get('default_controller'));
        self::$url['action']       = (isset($_GET['action']) ? $_GET['action'] : Config::get('default_action'));
        self::$url['arguments']    = isset($_GET['arg']) ? $_GET['arg'] : array();
        unset($_GET['controller'], $_GET['action'], $_GET['arg']); // This data shouldn't be available to GET
    }

    /**
     * Prevents the database class from being cloned
     * @return NULL
     */
    private function __clone() { }

    /**
     * Returns the singleton instance of the Database class
     * @return Request
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }

    /**
     * Returns some data from the GET superglobal, or NULL if not set
     * @param string $key
     * @return string|null
     */
    public function get($key) {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return NULL;
    }

    /**
     * Returns some data from the POST superglobal, or NULL if not set
     * @param string $key
     * @return string|null
     */
    public function post($key) {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return NULL;
    }

    /**
     * Returns some data from the COOKIE superglobal, or NULL if not set
     * @param string $key
     * @return string|null
     */
    public function cookie($key) {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        return NULL;
    }

    /**
     * Returns some data from the SERVER superglobal, or NULL if not set
     * @param string $key
     * @return string|null
     */
    public function server($key) {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        return NULL;
    }

    /**
     * Gets the controller variable from the URL, or the default if not present
     * @return string
     */
    public function urlController() {
        return self::$url['controller'];
    }

    /**
     * Gets the action variable from the URL, or the default if not present
     * @return string
     */
    public function urlAction() {
        return self::$url['action'];
    }

    /**
     * If $index is provided, returns the nth (zero based) URL argument (after controller and action)
     * E.G., if user requests /a/b/c/d/e, urlArguments(1) returns D
     * If $index is not provided, returns an array of all arguments
     * @param int $index
     * @return mixed
     */
    public function urlArguments($index = NULL) {
        if ($index != NULL) {
            if (isset(self::$url['arguments'][$index])) {
                return self::$url['arguments'][$index];
            } else {
                return NULL;
            }
        }
        return self::$url['arguments'];
    }
}
