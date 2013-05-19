<?php
namespace Sleek;

class Core {
    const CONTROLLER_PREFIX     = '\\App\\Controller_';
    const ACTION_PREFIX         = 'action_';
    const NO_ACTION             = 'noAction';

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

        $request->findRoute();

        $this->controllerName   = self::CONTROLLER_PREFIX . ucfirst($request->getController());
        $this->actionName       = self::ACTION_PREFIX . $request->getAction();
        $this->arguments        = $request->getRouteExtras();

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
        } else if (method_exists($this->controller, self::NO_ACTION)) {
            // If the controller exists, but the action doesn't, and we have a noAction, run that
            $this->controller->preAction();
            self::loadController($this->controller, self::NO_ACTION, $this->arguments);
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
        $errorControllerName = self::CONTROLLER_PREFIX . Config::get('error_controller');
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
            $controller = self::CONTROLLER_PREFIX . $controller;
        }

        call_user_func(
            array(
                $controller,
                $action
            ),
            $arguments
        );
    }
}
