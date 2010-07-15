<?php
/**
 *
 */

/**
 *
 */
class InputControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $field = sprintf("<input type=\"text\" name=\"%s\" value=\"%s\" size=\"80\" />",
            $this->_var,
            htmlentities($this->_obj->$func())
        );
        
        return $this->getWrapper($field);
    }
}
