<?php
/**
 *
 */

/**
 *
 */
class CheckboxesControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $checked_options = array();
        $checked = $this->_obj->$func();
        
        foreach ($checked as $checked_option) {
            $checked_options[] = $checked_option->uid();
        }
        
        $optClass = get_class($checked->getObject());
        $options = new $optClass();
        
        if (count($options->getCollection()->fetchAll()) == 0) {
            
            $field = "&nbsp;";
            
        } else {
            
            foreach ($options->getCollection() as $option) {
                $field .= sprintf("<input type=\"checkbox\" name=\"%s\" value=\"tick\"%s /> %s<br />\n",
                    $this->_var,
                    (in_array($option->uid(), $checked_options) ? " checked=\"checked\"" : ""),
                    $option->cite()
                );
            }
            
        }
        
        return $this->getWrapper($field);
    }
}
