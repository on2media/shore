<?php
/**
 *
 */

/**
 *
 */
class CheckboxControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $field = sprintf("<input type=\"checkbox\" name=\"%s\" value=\"tick\"%s />",
            $this->_var,
            ($this->_obj->$func() == TRUE ? " checked=\"checked\"" : "")
        );
        
        return $this->getWrapper($field);
    }
}
