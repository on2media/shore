<?php
/**
 * Router Library
 *
 * @package core
 */

/**
 * Router class
 */
class Router
{
    /**
     * Array to store the connected routes.
     *
     * @access protected
     * @var array
     */
    protected $_routes = array();

    /**
     * Constructor
     *
     * This is private so that it's not possible to call new Router() - use Router::getInstance()
     * instead.
     *
     * @access private
     */
    private function __construct()
    {

    }

    /**
     * Get Router Instance
     *
     * There is only ever one router - use this to get the active instance.
     *
     * @access public
     * @static
     */
    public static function &getInstance()
    {
        static $instance;
        if (!is_object($instance)) $instance = new Router();
        return $instance;
    }

    /**
     * Define a Route
     *
     * @access public
     * @static
     *
     * @param  string  $regexp      A regular expression of the URL for which this route is for
     * @param  string  $controller  Name of the controller to call
     * @param  string  $call        The method to call in the controller
     */
    public static function connect($regexp, $controller, $call="list")
    {
        $_this = Router::getInstance();

        $_this->_routes[$regexp] = array(
            "controller"  =>  $controller,
            "call"        =>  $call
        );
    }

    /**
     * Get Directions
     *
     * Calls the correct controller and method based on the current URL (uses _PAGE constant)
     *
     * @access public
     * @static
     */
    public static function route()
    {
        $_this = Router::getInstance();
		$routes = array();
        if($_this && (isset($_this->_routes) && !empty($_this->_routes) && is_array($_this->_routes))) {
	        foreach ($_this->_routes as $regexp => $route) {

	            if (preg_match($regexp, _PAGE, $matches)) {

	                $controllerClass = "{$route["controller"]}Controller";

	                $controller = new $controllerClass();
	                echo $controller->{$route["call"]}($matches);
	                return;

	            }

	        }
	        $routes = $_this->_routes;
        }
        exit("Unable to route the request through a controller: " . print_r($routes, true));
    }
}
