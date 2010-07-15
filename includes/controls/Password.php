<?php
/**
 *
 */

/**
 *
 */
class PasswordControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $field = sprintf("New Password: <input type=\"password\" name=\"%1\$s\" value=\"\" size=\"30\" /> &nbsp;\n" .
        "Confirm Password: <input type=\"password\" name=\"%1\$s\" value=\"\" size=\"30\" />",
            $this->_var
        );
        
        return $this->getWrapper($field);
    }
}
