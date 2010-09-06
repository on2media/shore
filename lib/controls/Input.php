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
        
        $field = sprintf("<input type=\"text\" name=\"%s\" value=\"%s\" size=\"%d\" />",
            $this->_prefix . $this->_var,
            htmlspecialchars($this->_obj->$func()),
            $this->getSize()
        );
        
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        if (isset($formData[$this->_prefix . $this->_var])) {
            $formData[$this->_prefix . $this->_var] = trim($formData[$this->_prefix . $this->_var]);
        }
        return parent::process($formData);
    }
    
    /**
     *
     */
    public function getSize()
    {
        return 80;
    }
}
