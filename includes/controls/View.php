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
        return $this->getWrapper(htmlentities($this->_obj->$func()));
    }
}
