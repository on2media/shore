<?php
/**
 *
 */

/**
 *
 */
class DateTimePickerControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $field = sprintf("<input type=\"text\" name=\"%s\" value=\"%s\" size=\"30\" class=\"datetime\" />",
            $this->_prefix . $this->_var,
            date("d F Y H:i", ($this->_obj->$func() == 0 ? time() : $this->_obj->$func()))
        );
        
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        if (isset($formData[$this->_prefix . $this->_var])) {
            $formData[$this->_prefix . $this->_var] = @strtotime($formData[$this->_prefix . $this->_var]);
        }
        
        return parent::process($formData);
    }
}
