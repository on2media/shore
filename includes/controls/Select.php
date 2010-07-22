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
        $optionClass = $this->_objType;
        
        //echo "<pre>" . print_r( $this->_obj->$func() , TRUE) . "</pre>";
        
        $options = new $optionClass();
        if (count($options->getCollection()->fetchAll()) == 0) {
            
            $field = "&nbsp;";
            
        } else {
            
            $field = sprintf("<select name=\"%s\">\n    <option value=\"0\">&nbsp;</option>", $this->_var);
            
            foreach ($options->getCollection() as $option) {
                $field .= sprintf("    <option value=\"%s\"%s>%s</option>\n",
                    $option->uid(),
                    ($this->_obj->$func() instanceof $optionClass && $this->_obj->$func()->uid() == $option->uid() ? " selected=\"selected\"":""),
                    $option->cite()
                );
            }
            
            $field .= "</select>";
            
        }
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        $func = "get" . var2func($this->_var);
        $optionClass = get_class($this->_obj->$func());
        
        if (isset($formData[$this->_var])) {
            
            $obj = new $optionClass();
            $formData[$this->_var] = $obj->fetchById($formData[$this->_var]);
            
        }
        
        return parent::process($formData);
    }
}
