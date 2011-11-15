<?php
namespace Sleek;

class Response {
    /**
     * @var Response The singleton instance of our response class
     */
    static private $_instance   = NULL;

    private function __construct() {

    }

    /**
     * Prevents the class from being cloned
     * @return NULL
     */
    private function __clone() { }

    /**
     * Returns the singleton instance of the Database class
     * @return Response
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new Response();
        }
        return self::$_instance;
    }

    /**
     * Redirects the user to the specified page
     * @static
     * @param string $url URL to redirect user to
     * @param bool $permanent If true, sends a 301 permanent
     * @return bool Whether or not the header was set properly
     */
    public static function redirect($url, $permanent = FALSE) {
        if (headers_sent()) {
            return FALSE;
        }
        if ($permanent) {
            header("HTTP/1.1 301 Moved Permanently");
        }
        header("Location: $url");
        return TRUE;
    }

    /**
     * Sends an HTTP header to the client
     * @static
     * @param string $name The name of the HTTP header
     * @param string $value The value of the HTTP header
     * @return bool Whether or not the header was set
     */
    public static function header($name, $value = NULL) {
        if (headers_sent()) {
            return FALSE;
        }
        $header = $name;
        if ($value) {
            $header .= ": $value";
        }
        header($header);
        return TRUE;
    }

    /**
     * Sets a status code by taking the number and automatically adding the status text for the developer
     * @link http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
     * @todo List needs to contain more entries... All of them?
     * @param int $code
     * @return bool
     */
    public static function status($code) {
        if (headers_sent()) {
            return FALSE;
        }
        
        $text = '';
        switch($code) {
            case '200':
                $text = "OK";
                break;
            case '400':
                $text = "Bad Request";
                break;
            case '401':
                $text = "Unauthorized";
                break;
            case '403':
                $text = "Forbidden";
                break;
            case '404':
                $text = "Not Found";
                break;
            case '500':
                $text = "Internal Server Error";
                break;
            default:
                return FALSE;
        }
        header('HTTP/1.1 ' . $code . ' ' . $text);
        return TRUE;
    }

    /**
     * Renders a view and sends it to the browser. Can't be used to return strings.
     * To return a string, use \Sleek\View::render($file, $data, TRUE) instead.
     * @param string $file Path to view file to render, minus the extension
     * @param array $data Data array, ran through extract()
     * @return void
     */
    public function view($file, $data = array()) {
        \Sleek\View::render($file, $data);
    }

    /**
     * This function sets a cookie. To read a cookie, use Request::cookie();
     * @static
     * @param string $name Name of the cookie to use
     * @param mixed $value The value of the cookie being set
     * @param int $expire Expiration date
     * @param null $path Cookie Path
     * @param null $domain Cookie Domain
     * @param null $secure Cookie Secure
     * @param null $httponly Cookie HTTPOnly
     * @return bool Success or failure
     */
    public static function cookie($name, $value, $expire = 0, $path = NULL, $domain = NULL, $secure = NULL, $httponly = NULL) {
        if (headers_sent()) {
            return FALSE;
        }
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}
