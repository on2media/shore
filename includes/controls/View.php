<?php
/**
 *
 */

/**
 *
 */
class ViewControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "cite" . var2func($this->_var);
        $field = htmlentities($this->_obj->$func());
        return $this->getWrapper(($field == "" ? "&nbsp;" : $field));
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        return TRUE;
    }
}
