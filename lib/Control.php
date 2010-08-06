<?php
/**
 *
 */

/**
 *
 */
abstract class Control
{
    /**
     *
     */
    protected $_obj = NULL;
    
    /**
     *
     */
    protected $_var = "";
    
    /**
     *
     */
    protected $_heading = "";
    
    /**
     *
     */
    protected $_tip = "";
    
    /**
     *
     */
    protected $_validation = array();
    
    /**
     *
     */
    protected $_required = FALSE;
    
    /**
     *
     */
    protected $_objType;
    
    /**
     *
     */
    protected $_showValidation = FALSE;    
    
    /**
     *
     */
    protected $_error = "";
    
    /**
     *
     */
    public function __construct(Object $obj, $var, $fieldSpec=array())
    {
        $this->_obj = $obj;
        $this->_var = $var;
        
        $heading = (isset($fieldSpec["heading"]) ? $fieldSpec["heading"] : NULL);
        $this->_heading = ($heading != NULL ? $heading : var2label($var));
        
        $tip = (isset($fieldSpec["on_edit"]["tip"]) ? $fieldSpec["on_edit"]["tip"] : NULL);
        $this->_tip = ($tip != NULL ? $tip : "");
        
        if (isset($fieldSpec["validation"])) $this->_validation = $fieldSpec["validation"];
        
        if (isset($fieldSpec["required"]) && $fieldSpec["required"] == TRUE) $this->_required = TRUE;
        
        if ($this->_obj->typeOf($var) == "object") {
            $pieces = explode(":", $fieldSpec["type"], 2);
            if (isset($pieces[1])) $this->_objType = $pieces[1] . "Object";
        }
        
    }
    
    /**
     *
     */
    public function getWrapper($field="&nbsp;")
    {
        $rtn = sprintf("<p>\n    <label>%s%s%s</label>\n    %s\n%s</p>\n",
            htmlentities($this->_heading),
            ($this->_required ? "<em>*</em>" : ""),
            ($this->_tip != NULL ? sprintf("<span class=\"tip\">%s</span>", $this->_tip) : ""),
            $field,
            ($this->_showValidation == TRUE && $this->getError()
                ? "<small>" . htmlentities($this->getError()) . "</small>\n"
                : ""
            )
        );
        
        return $rtn;
    }
    
    /**
     *
     */
    public function getVar()
    {
        return $this->_var;
    }
    
    /**
     *
     */
    public function getError()
    {
        return ($this->_error != "" ? $this->_error : FALSE);
    }
    
    /**
     *
     */
    abstract public function output();
    
    /**
     *
     */
    public function process(array $formData)
    {
        $this->_showValidation = TRUE;
        
        if (!isset($formData[$this->_var]) && !is_null($formData[$this->_var])) {
            
            $this->_error = "Field was missing from the received form data.";
            $this->_obj->{$this->_var} = $formData[$this->_var] = FALSE;
            
        } else {
            
            $this->_obj->{$this->_var} = $formData[$this->_var];
            
        }
    }
    
    /**
     *
     */
    public function validate()
    {
        $value = $this->_obj->{$this->_var};
        
        if ($this->_error != "") return FALSE;
        
        if ($this->_required == FALSE) {
            
            if ($value instanceof Object && $value->getCollection()->count() == 0) return TRUE;
            else if ($value === NULL) return TRUE;
            
        } else if (empty($value)) {
            
            $this->_error = "This field is required.";
            return FALSE;
            
        }
        
        foreach ($this->_validation as $rule => $opts) {
            
            $fail = FALSE;
            
            switch ($rule) {
                
                case "object":
                    $modelObject = $opts["object"] . "Object";
                    $fail = (!is_object($value) || !$value instanceof $modelObject);
                    $message = "Please select from the list.";
                    break;
                
                case "timestamp":
                    $fail = (!@date("U", $value));
                    $message = "Please enter a valid date/time.";
                    break;
                
                case "regexp":
                    $subject = (is_object($value) ? $value->uid() : $value);
                    $fail = (!preg_match($opts["test"], $subject));
                    $message = "This field is invalid.";
                    break;
                
                case "unique":
                    
                    $className = get_class($this->_obj);
                    $obj = new $className();
                    
                    $collection = $obj->getCollection();
                    $collection->setLimit($this->_var, "=", $value);
                    
                    foreach ($collection->fetchAll() as $other) {
                        
                        if ($other->uid() != $this->_obj->uid()) {
                            
                            $fail = TRUE;
                            $message = "This field must be unique.";
                            break(2);
                            
                        }
                        
                    }
                    
                    break;
                
            }
            
            if ($fail) {
                $this->_error = (isset($opts["message"]) ? $opts["message"] : $message);
                return FALSE;
            }
            
        }
        
        return TRUE;
    }
}
