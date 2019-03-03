<?php
/**
 * @package core
 */

/**
 *
 */
class Session
{
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
        // session_set_save_handler(
        //     array(&$this, "sessionOpen"),
        //     array(&$this, "sessionClose"),
        //     array(&$this, "sessionRead"),
        //     array(&$this, "sessionWrite"),
        //     array(&$this, "sessionDestroy"),
        //     array(&$this, "sessionGarbageCollector")
        // );

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
        if (substr($name, 0, 3) == "get") {

            $name = substr($name, 3);
            if (array_key_exists($name, $_SESSION)) return $_SESSION[$name];

        } else if (substr($name, 0, 5) == "unset") {

            $name = substr($name, 5);
            unset($_SESSION[$name]);
            return TRUE;

        } else {

            if (isset($arguments) && is_array($arguments) && isset($arguments[0])) {

                if (substr($name, 0, 3) == "set" && (isset($arguments[0]) || is_null($arguments[0]))) {

                    $name = substr($name, 3);
                    $_SESSION[$name] = $arguments[0];
                    return TRUE;

                }

            }
        }

        return FALSE;
    }

    /**
     *
     */
    public function sessionOpen($save_path, $session_name)
    {
        return TRUE;
    }

    /**
     *
     */
    public function sessionClose()
    {
        return TRUE;
    }

    /**
     *
     */
    public function sessionRead($id)
    {
        $sessionObj = new SessionObject();
        if ($session = $sessionObj->fetchById($id)) {
            return (string)$session->getData();
        }

        return "";
    }

    /**
     *
     */
    public function sessionWrite($id, $data)
    {
        $sessionObj = new SessionObject();
        if (!$session = $sessionObj->fetchById($id)) {
            $session = new SessionObject();
            $session->setId($id);
        }

        $session->setData($data);
        $session->setLastModified(time());

        return $session->save();
    }

    /**
     *
     */
    public function sessionDestroy($id)
    {
        $sessionObj = new SessionObject();
        if ($session = $sessionObj->fetchById($id)) {
            return $session->delete();
        }

        return FALSE;
    }

    /**
     *
     */
    public function sessionGarbageCollector($maxlifetime=NULL)
    {
        if ($maxlifetime === NULL) $maxlifetime = ini_get("session.gc_maxlifetime");

        $sessionObj = new SessionObject();
        $sessions = $sessionObj->getCollection();

        if($sessions instanceof Collection) {
        	$sessions->setLimit("last_modified", "<", date("Y-m-d H:i:s", time() - $maxlifetime));
        	foreach ($sessions->fetchAll() as $session) {
        		$session->delete();
        	}
        }

        return TRUE;
    }

    /**
     *
     */
    public function __destruct()
    {
        @session_write_close();
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
