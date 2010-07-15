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
            $this->_var,
            date("d F Y H:i", $this->_obj->$func())
        );
        
        return $this->getWrapper($field);
    }
}
