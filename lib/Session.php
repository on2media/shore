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
        session_name(SESSION_NAME);
        @session_start();
    }
    
    /**
     * Get Session Instance
     *
     * There is only ever one session instance - use this to get the active instance.
     *
     * @access public
     * @static
     */
    public static function &getInstance()
    {
        static $instance;
        if (!is_object($instance)) {
            
            $instance = new Session();
            
        }
        return $instance;
    }
    
    /**
     *
     */
    public function getUsernameHeading()
    {
        $userObject = AUTH_MODEL; $users = new $userObject();
        return $users->getFieldHeading(AUTH_USERNAME);
    }
    
    /**
     *
     */
    public function getPasswordHeading()
    {
        $userObject = AUTH_MODEL; $users = new $userObject();
        return $users->getFieldHeading(AUTH_PASSWORD);
    }
    
    /**
     *
     */
    public function login($username, $password)
    {
        $userObject = AUTH_MODEL;
        $users = new $userObject();
        
        $users->getCollection()->setLimit(AUTH_USERNAME, "=", $username);
        $users->getCollection()->setLimit(AUTH_PASSWORD, "=", md5($password));
        
        if(!$currentUser = $users->getCollection()->fetchFirst()) {
            
            return FALSE;
            
        } else {
            
            $this->setUser($currentUser);
            return TRUE;
            
        }
    }
    
    /**
     *
     */
    public function logout()
    {
        return $this->unsetUser();
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
            if (isset($_SESSION[$name])) return $_SESSION[$name];
            
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
}
