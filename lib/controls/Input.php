<?php
/**
 *
 */

/**
 *
 */
class InputControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $func = "get" . var2func($this->_var);
        
        $field = sprintf("<input type=\"text\" name=\"%s\" value=\"%s\"%s />",
            $this->_prefix . $this->_var,
            htmlspecialchars(($this->_obj->$func() instanceof Object ? $this->_obj->$func()->uid() : $this->_obj->$func())),
            ($this->usingBootstrap()
                ? sprintf(" class=\"%s\"", $this->getBootstrapSize())
                : sprintf(" size=\"%d\"", $this->getSize())
            )
        );
        
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        if (isset($formData[$this->_prefix . $this->_var])) {
            $formData[$this->_prefix . $this->_var] = trim($formData[$this->_prefix . $this->_var]);
        }
        return parent::process($formData);
    }
    
    /**
     *
     */
    public function getSize()
    {
        return 80;
    }

    /**
     * Converts input size attribute into a suitable class for Bootstrap
     *
     * @returns  string
     */
    public function getBootstrapSize()
    {
        if ($this->getSize() < 12) return "input-mini";
        if ($this->getSize() < 20) return "input-small";
        if ($this->getSize() < 40) return "input-medium";
        if ($this->getSize() < 60) return "input-large";
        if ($this->getSize() < 80) return "input-xlarge";
        
        return "input-xxlarge";
    }
}
