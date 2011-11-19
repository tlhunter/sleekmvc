<?php
namespace Sleek;

class Core {
    /**
     * @var string
     */
    protected $controllerName   = NULL;

    /**
     * @var \Sleek\Controller_Base
     */
    protected $controller       = NULL;

    /**
     * @var string
     */
    protected $actionName       = NULL;

    /**
     * @var array
     */
    protected $arguments        = NULL;

    /**
     * Builds the request, tries to find the controller, executes pre action, action, post action, or 404
     */
    public function __construct() {
        $request = Request::getInstance();
        $this->controllerName   = '\\App\\Controller_' . $request->urlController();
        $this->actionName       = 'action_' . $request->urlAction();
        $this->arguments        = $request->urlArguments();

        try {
            $this->controller = new $this->controllerName;
        } catch (Exception_ClassNotFound $e) {
            self::display404();
        }

        if (method_exists($this->controller, $this->actionName)) {
            // If the controller and action exist, we run that.
            $this->controller->preAction();
            self::loadController($this->controller, $this->actionName, $this->arguments);
            $this->controller->postAction();
        } else if (method_exists($this->controller, 'noAction')) {
            // If the controller exists, but the action doesn't, and we have a noAction, run that
            $this->controller->preAction();
            self::loadController($this->controller, 'noAction', $this->arguments);
            $this->controller->postAction();
        } else {
            // Oh no, I can't find anything! Just run the 404 stuff!
            self::display404();
        }
    }

    /**
     * Executes the 404 app controller action
     * @static
     * @return void
     */
    public static function display404() {
        $errorControllerName = '\\App\\Controller_' . Config::get('error_controller');
        $errorController = new $errorControllerName;
        self::loadController(
            $errorController,
            'action_404'
        );
        exit();
    }

    /**
     * Executes the specified controller action, passing along arguments
     * @static
     * @param string|Controller_Base $controller
     * @param string $action
     * @param array $arguments
     * @return void
     */
    public static function loadController($controller, $action, $arguments = array()) {
        if (is_string($controller)) {
            $controller = '\\App\\Controller_' . $controller;
        }
        call_user_func_array(
            array(
                $controller,
                $action
            ),
            $arguments
        );
    }
}
