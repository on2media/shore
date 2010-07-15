<?php
/**
 *
 */

/**
 *
 */
class SelectControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        $optionClass = get_class($this->_obj->$func());
        
        $options = new $optionClass();
        if (count($options->getCollection()->fetchAll()) == 0) {
            
            $field = "&nbsp;";
            
        } else {
            
            $field = sprintf("<select name=\"%s\">\n    <option value=\"0\">&nbsp;</option>", $this->_var);
            
            foreach ($options->getCollection() as $option) {
                $field .= sprintf("    <option value=\"%s\"%s>%s</option>\n",
                    $option->uid(),
                    ($this->_obj->$func()->uid() == $option->uid() ? " selected=\"selected\"":""),
                    $option->cite()
                );
            }
            
            $field .= "</select>";
            
        }
        return $this->getWrapper($field);
    }
}
