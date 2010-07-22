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
    
    /**
     *
     */
    public function process(array $formData)
    {
        if (isset($formData[$this->_var])) $formData[$this->_var] = trim($formData[$this->_var]);
        return parent::process($formData);
    }    
}
