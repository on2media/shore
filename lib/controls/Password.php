<?php
/**
 *
 */

/**
 *
 */
class PasswordControl extends Control
{
    /**
     *
     */
    public function output()
    {
        $field = sprintf("New Password: <input type=\"password\" name=\"%1\$s[]\" value=\"\" size=\"30\" /> &nbsp;\n" .
        "Confirm Password: <input type=\"password\" name=\"%1\$s[]\" value=\"\" size=\"30\" />",
            $this->_prefix . $this->_var
        );
        
        return $this->getWrapper($field);
    }
    
    /**
     *
     */
    public function process(array $formData)
    {
        if (isset($formData[$this->_prefix . $this->_var])) {
            
            $value =& $formData[$this->_prefix . $this->_var];
            
            if (!is_array($value)) {
                
                $value = FALSE;
                $this->_error = "Unexpected password value.";
                
            } else {
                
                $unique = array_unique($value);
                $value = reset($value);
                
                if (count($unique) != 1) $this->_error = "The passwords entered did not match.";
                
                if ($value == "") {
                    $value = $this->_obj->{$this->_var};
                } else {
                    $value = md5($value);
                }
                
            }
            
        }
        
        return parent::process($formData);
    }
}
