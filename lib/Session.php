<?php
/**
 * @package core
 */

/**
 *
 */
class Session
{
    private string $namespace = 'shore';

    /**
     * Constructor
     *
     * This is private so that it's not possible to call new Session() - use Session::getInstance()
     * instead.
     *
     * @access private
     */
    private function __construct()
    {
        session_name(SESSION_NAME);
        session_start();
    }

    /**
     * Get Session Instance
     *
     * There is only ever one session instance - use this to get the active instance.
     *
     * @access public
     * @static
     */
    public static function getInstance()
    {
        static $instance;
        if (!is_object($instance)) $instance = new Session();
        return $instance;
    }

    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     *
     */
    public function __get($name)
    {
        $func = "get" . var2func($name);
        return $this->$func();
    }

    /**
     *
     */
    public function __set($name, $value)
    {
        $func = "set" . var2func($name);
        return $this->$func($value);
    }

    /**
     *
     */
    public function __call($name, $arguments)
    {
        if (!array_key_exists($this->namespace, $_SESSION)) {
            $_SESSION[$this->namespace] = [];
        }

        if (substr($name, 0, 3) == "get") {

            $name = substr($name, 3);
            if (array_key_exists($name, $_SESSION[$this->namespace])) return $_SESSION[$this->namespace][$name];

        } else if (substr($name, 0, 5) == "unset") {

            $name = substr($name, 5);
            unset($_SESSION[$this->namespace][$name]);
            return TRUE;

        } else {

            if (isset($arguments) && is_array($arguments) && isset($arguments[0])) {

                if (substr($name, 0, 3) == "set" && (isset($arguments[0]) || is_null($arguments[0]))) {

                    $name = substr($name, 3);
                    $_SESSION[$this->namespace][$name] = $arguments[0];
                    return TRUE;

                }

            }
        }

        return FALSE;
    }

    /**
     *
     */
    public function respawn()
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {

            // Remove session cookie
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 3600, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);

        }

        session_destroy();
        session_start();
    }

    public function removeNamespaceFilters($namespace = null) {
        $sessionFilters = $this->getAllSessionFilters();
        if($sessionFilters || is_array($sessionFilters)) {
            if(isset($sessionFilters[$namespace])) {
                unset($sessionFilters[$namespace]);
            }
        }
        $this->setAllSessionFilters($sessionFilters);
    }

    public function getNamespaceFilters($namespace = null) {
        $sessionFilters = $this->getAllSessionFilters();
        if(
                !is_null($sessionFilters[$namespace]) &&
                is_array($sessionFilters[$namespace]) &&
                (count($sessionFilters[$namespace]) > 0) &&
                (!(count($sessionFilters[$namespace]) == 1 && $sessionFilters[$namespace]['page']))
         ) {
            return $sessionFilters[$namespace];
        }else {
            return null;
        }
    }
}
