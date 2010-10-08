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
        
        if ($checked instanceof Collection) {
            foreach ($checked as $checked_option) {
                $checked_options[] = $checked_option->uid();
            }
        }
        
        $options = $this->getOptions()->fetchAll();
        
        $field = "";
        
        if ($options->count() > 0) {
            
            foreach ($options as $option) {
                $field .= sprintf("    <label><input type=\"checkbox\" name=\"%s[]\" value=\"%s\"%s /> %s</label>\n",
                    $this->_prefix . $this->_var,
                    $option->uid(),
                    (in_array($option->uid(), $checked_options) ? " checked=\"checked\"" : ""),
                    $option->cite()
                );
            }
            
            $field = "<span class=\"cb_list\">\n" . $field . "</span>\n";
            
        }
        
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        $options = $this->getOptions();
        
        if (isset($formData[$this->_prefix . $this->_var]) && is_array($formData[$this->_prefix . $this->_var])) {
            
            $options->setLimit($options->getObject()->uidField(), "IN", $formData[$this->_prefix . $this->_var]);
            $options->fetchAll();
            
        }
        
        $formData[$this->_prefix . $this->_var] = $options;
        return parent::process($formData);
    }
    
    /**
     *
     */
    public function getOptions()
    {
        $func = "get" . var2func($this->_var);
        $optClass = get_class($this->_obj->$func()->getObject());
        $options = new $optClass();
        return $options->getCollection();
    }
}
