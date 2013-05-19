<?php
namespace Sleek;

class Response {
    /**
     * @var Response The singleton instance of our response class
     */
    static private $_instance = NULL;

    /**
     * @var array A listing of HTTP status codes
     * @author http://coreymaynard.com/blog/creating-a-restful-api-with-php/
     */
    static public $status = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    private function __construct() { }

    /**
     * Prevents the class from being cloned
     * @return null
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
        if (headers_sent() || !isset(self::$status[$code])) {
            return FALSE;
        }

        header("HTTP/1.1 $code " . self::$status[$code]);

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
