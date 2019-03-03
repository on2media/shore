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
        $field = htmlspecialchars($this->_obj->$func());
        return $this->getWrapper($field);
    }

    /**
     *
     */
    public function process(array $formData)
    {
        return TRUE;
    }
}
