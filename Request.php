<?php
namespace Sleek;

class Request {
    /**
     * These values need to match the values used in the .htaccess file
     */
    const GET_VAR_CONTROLLER    = 'controller';
    const GET_VAR_ACTION        = 'action';
    const GET_VAR_ARGUMENTS     = 'arguments';

    /**
     * @var Request The singleton instance of our request class
     */
    static private $_instance   = NULL;

    /**
     * @var array Array containing URL data
     */
    static protected $url       = array();

    /**
     * Initializes the Request singleton, sets data from $_GET variables
     */
    private function __construct() {
        self::$url[self::GET_VAR_CONTROLLER]   = (isset($_GET[self::GET_VAR_CONTROLLER]) ? ucfirst($_GET[self::GET_VAR_CONTROLLER]) : Config::get('default_controller'));
        self::$url[self::GET_VAR_ACTION]       = (isset($_GET[self::GET_VAR_ACTION]) ? $_GET[self::GET_VAR_ACTION] : Config::get('default_action'));
        self::$url[self::GET_VAR_ARGUMENTS]    = isset($_GET[self::GET_VAR_ARGUMENTS]) ? $_GET[self::GET_VAR_ARGUMENTS] : array();

        // We don't want the URL paramaters accessible via GET
        unset(
            $_GET[self::GET_VAR_CONTROLLER],
            $_GET[self::GET_VAR_ACTION],
            $_GET[self::GET_VAR_ARGUMENTS]
        );
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
        return self::$url[self::GET_VAR_CONTROLLER];
    }

    /**
     * Gets the action variable from the URL, or the default if not present
     * @return string
     */
    public function urlAction() {
        return self::$url[self::GET_VAR_ACTION];
    }

    /**
     * If $index is provided, returns the nth (zero based) URL argument (after controller and action)
     * E.G., if user requests /a/b/c/d/e, urlArguments(1) returns D
     * If $index is not provided, returns an array of all arguments
     * Incorporates a bugfix by @_wzee
     * @param int $index
     * @return mixed
     */
    public function urlArguments($index = NULL) {
        if ($index !== NULL) {
            if (isset(self::$url[self::GET_VAR_ARGUMENTS][$index])) {
                return self::$url[self::GET_VAR_ARGUMENTS][$index];
            } else {
                return NULL;
            }
        }
        return self::$url[self::GET_VAR_ARGUMENTS];
    }
}
