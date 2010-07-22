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
                $field .= sprintf("<input type=\"checkbox\" name=\"%s[]\" value=\"%s\"%s /> %s<br />\n",
                    $this->_var,
                    $option->uid(),
                    (in_array($option->uid(), $checked_options) ? " checked=\"checked\"" : ""),
                    $option->cite()
                );
            }
            
        }
        
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        $func = "get" . var2func($this->_var);
        $checked = $this->_obj->$func();
        
        $optClass = get_class($checked->getObject());
        $options = new $optClass();
        
        if (isset($formData[$this->_var]) && is_array($formData[$this->_var])) {
            
            $options->getCollection()->setLimit($options->uidField(), "IN", $formData[$this->_var]);
            $options->getCollection()->fetchAll();
            
        }
        
        $formData[$this->_var] = $options->getCollection();
        return parent::process($formData);
    }
}
