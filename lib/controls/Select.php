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
        $options = $this->getOptions();
        
        $field = "";
        
        if ($options->fetchAll()->count() > 0) {
            
            $field = sprintf("<select name=\"%s\">\n    <option value=\"0\">&nbsp;</option>", $this->_prefix . $this->_var);
            
            foreach ($options as $option) {
                $field .= sprintf("    <option value=\"%s\"%s>%s</option>\n",
                    $option->uid(),
                    ($this->_obj->$func() instanceof $this->_objType && $this->_obj->$func()->uid() == $option->uid() ? " selected=\"selected\"":""),
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
        $options = $this->getOptions();
        
        if (!isset($formData[$this->_prefix . $this->_var])) {
            
            $formData[$this->_prefix . $this->_var] = NULL;
            
        } else {
            
            if ($formData[$this->_prefix . $this->_var] == "0") $formData[$this->_prefix . $this->_var] = NULL;
            else {
                
                $options->setLimit($options->getObject()->uidField(), "=", $formData[$this->_prefix . $this->_var]);
                if (!$formData[$this->_prefix . $this->_var] = $options->fetchFirst()) {
                    $formData[$this->_prefix . $this->_var] = NULL;
                }
                
            }
            
        }
        
        return parent::process($formData);
    }
    
    /**
     *
     */
    public function getOptions()
    {
        $func = "get" . var2func($this->_var);
        $optionClass = $this->_objType;
        $options = new $optionClass();
        return $options->getCollection();
    }
}
