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
    
    /**
     *
     */
    public function process(array $formData)
    {
        $formData[$this->_var] = (isset($formData[$this->_var]));
        return parent::process($formData);
    }
}
