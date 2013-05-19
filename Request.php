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
    static protected $url       = array();

    /**
     * @var array Array containing all registered routes
     */
    static protected $routes    = array();

    /**
     * @var array representation of the GET params (which are blown away somehow)
     */
    static protected $getParams = array();

    /**
     * @var array The matched controller and action for the current request
     */
    static protected $route     = array(
        'controller'            => NULL,
        'action'                => NULL
    );

    /**
     * @var array Extra information captured from the URL with the current request
     */
    static protected $routeExtras = array();

    /**
     * Initializes the Request singleton, sets data from $_GET variables
     */
    private function __construct() { }

    /**
     * Prevents the request class from being cloned
     * @return null
     */
    private function __clone() { }

    /**
     * This lets the developer define a custom route. The first argument is a route string, the second is default information
     *
     * Magical route strings are 'controller' and 'action'.
     *
     * @static
     * @param string $route
     * @param array $defaults
     * @see http://kohanaframework.org/3.0/guide/kohana/routing
     */
    public static function addRoute($route, $defaults) {
        // PROVIDED PATTERN:    (/:controller(/:action(/:id)))    This format was inspired by Kohana
        // OUTPUT REGEX:        ^(/(?P<controller>[a-zA-Z0-9_-]+)(/(?P<action>[a-zA-Z0-9_-]+)(/(?P<id>[a-zA-Z0-9_-]+))?)?)?/?$
        // ADD a #^ at beginning
        // ADD a /?# at end
        // ( turns into (
        // ) turns into )?
        // :capture_name turns into (?P<$1>[a-zA-Z0-9_-]+)

        $route = '#^' . Config::get('base_url') . $route . '/?#';
        $route = str_replace(')', ')?', $route);
        $route = preg_replace('#\:([a-z_]+)#', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
        self::$routes[$route] = $defaults;
    }

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
     * Returns some data from the get request, an array if no key is provided, or NULL if not set
     * @param null|string $key
     * @return string|array|null
     */
    public function get($key = NULL) {
        if (is_null($key)) {
            return self::$getParams;
        }

        if (isset(self::$getParams[$key])) {
            return self::$getParams[$key];
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
     * @return null|string
     */
    public function getController() {
        return self::$route['controller'];
    }

    /**
     * Gets the action variable from the URL, or the default if not present
     * @return null|string
     */
    public function getAction() {
        return self::$route['action'];
    }

    /**
     * Gets an array of the extra route items captured, e.g. the :id
     * @return array The matched route extras
     */
    public function getRouteExtras() {
        return self::$routeExtras;
    }

    /**
     * Attempts to find the route for the current request, also triggers the hunt for get params
     *
     * @param null|string $url An optional url to check against. If not set will use $_SERVER['REQUEST_URL']
     * @return array Matching route information (self::$route)
     */
    public function findRoute($url = NULL) {
        $url = $url ?: $_SERVER['REQUEST_URI'];
        self::findGetParams($url);

        foreach(self::$routes AS $pattern => $defaults) {
            if (preg_match($pattern, $url, $matches)) {
                // Found our matching route!
                self::$route['controller'] = isset($matches['controller']) ? $matches['controller'] : $defaults['controller'];
                self::$route['action'] = isset($matches['action']) ? $matches['action'] : $defaults['action'];
                foreach($defaults AS $key => $value) {
                    // preg_match stores a bunch of integer keys and string, only want the strings
                    if (is_string($key) && $key != 'controller' && $key != 'action') {
                        self::$routeExtras[$key] = isset($matches[$key]) ? $matches[$key] : $value;
                    }
                }

                return self::$route;
            }
        }
    }

    /**
     * Parses the URL to find any get paramaters
     *
     * @param string $url The URL of the request (starting with the first slash)
     * @return array An associative array of get keys to values
     */
    private static function findGetParams($url) {
        if (strpos($url, '?') !== FALSE) {
            $parts = explode('?', $url);
            if ($parts[1]) {
                parse_str($parts[1], self::$getParams);
                return self::$getParams;
            }
        }

        return self::$getParams = array();
    }

}
