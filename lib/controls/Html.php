<?php
/**
 *
 */

/**
 *
 */
class HtmlControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $field = sprintf("<textarea name=\"%s\" cols=\"80\" rows=\"20\" class=\"wysiwyg\">%s</textarea>",
            $this->_prefix . $this->_var,
            htmlspecialchars($this->_obj->$func())
        );
        
        return $this->getWrapper($field);
    }
}
