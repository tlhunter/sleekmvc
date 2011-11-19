<?php
namespace Sleek;

/**
 * Class for dispatching the error handler in the application code
 */
class Error {
    /**
     * Attempts to handle errors using action_500
     * @static
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param $errcontext
     * @return void
     */
    public static function handler($errno, $errstr, $errfile, $errline, $errcontext) {
        $errorClassName = '\\App\\Controller_' . Config::get('error_controller');
        /**
         * @var $errorClass \App\Controller_Error
         */
        $errorClass = new $errorClassName;
        $errorClass->action_500($errno, $errstr, $errfile, $errline, $errcontext);
        exit();
    }

    /**
     * Attempts to catch fatal errors using action_fatal
     * @static
     * @return void
     */
    public static function shutdown() {
        $error = error_get_last();
        if ($error['type'] == 1) {
            $errorClassName = '\\App\\Controller_' . Config::get('error_controller');
            /**
             * @var $errorClass \App\Controller_Error
             */
            $errorClass = new $errorClassName;
            $errorClass->action_fatal($error);
        }
    }

    /**
     * Registers the error handler
     * @static
     * @return void
     */
    public static function register() {
        set_error_handler(array('\\Sleek\\Error', 'handler'));
    }
}
