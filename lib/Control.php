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
    protected $_prefix = "";
    
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
    protected $_showEmpty = TRUE;
    
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
    protected $_fieldPrefix = "";
    
    /**
     *
     */
    protected $_fieldSuffix = "";
    
    /**
     *
     */
    public function __construct(Object $obj, $prefix, $var, $fieldSpec=array())
    {
        $this->_obj = $obj;
        
        $this->_prefix = $prefix;
        $this->_var = $var;
        
        $heading = (isset($fieldSpec["heading"]) ? $fieldSpec["heading"] : NULL);
        $this->_heading = ($heading != NULL ? $heading : var2label($var));
        
        $tip = (isset($fieldSpec["on_edit"]["tip"]) ? $fieldSpec["on_edit"]["tip"] : NULL);
        $this->_tip = ($tip != NULL ? $tip : "");
        
        if (isset($fieldSpec["validation"])) $this->_validation = $fieldSpec["validation"];
        
        if (isset($fieldSpec["required"]) && $fieldSpec["required"] == TRUE) $this->_required = TRUE;
        
        if (isset($fieldSpec["on_edit"]["show_empty"]) && $fieldSpec["on_edit"]["show_empty"] == FALSE) $this->_showEmpty = FALSE;
        
        if ($this->_obj->typeOf($var) == "object") {
            $pieces = explode(":", $fieldSpec["type"], 2);
            if (isset($pieces[1])) $this->_objType = $pieces[1] . "Object";
        }
        
        if (isset($fieldSpec["prefix"])) $this->_fieldPrefix = $fieldSpec["prefix"];
        if (isset($fieldSpec["suffix"])) $this->_fieldSuffix = $fieldSpec["suffix"];
    }
    
    /**
     *
     */
    public function getWrapper($field="")
    {
        if ($field == "" && !$this->_showEmpty) return "";
        
        $rtn = sprintf("<p>\n    <label>%s%s%s</label>\n    %s%s%s\n%s</p>\n",
            htmlspecialchars($this->_heading),
            ($this->_required ? "<em>*</em>" : ""),
            ($this->_tip != NULL ? sprintf("<span class=\"tip\">%s</span>", $this->_tip) : ""),
            ($this->_fieldPrefix == "" ? "" : $this->_fieldPrefix . " "),
            ($field == "" ? "&nbsp;" : $field),
            ($this->_fieldSuffix == "" ? "" : " " . $this->_fieldSuffix),
            ($this->_showValidation == TRUE && $this->getError()
                ? "<" . CONTROL_ERROR_TAG . ">" . htmlspecialchars($this->getError()) . "</" . CONTROL_ERROR_TAG . ">\n"
                : ""
            )
        );
        
        return $rtn;
    }
    
    /**
     *
     */
    public function getObject()
    {
        return $this->_obj;
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
    public function setError($error)
    {
        $this->_error = $error;
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
        
        if (!array_key_exists($this->_prefix . $this->_var, $formData)) {
            
            $this->setError("Field was missing from the received form data.");
            $this->_obj->{$this->_var} = $formData[$this->_prefix . $this->_var] = FALSE;
            
        } else {
            
            $this->_obj->{$this->_var} = $formData[$this->_prefix . $this->_var];
            
        }
    }
    
    /**
     *
     */
    public function validate()
    {
        $value = $this->_obj->{$this->_var};
        
        if ($this->getError() != "") return FALSE;
        
        if ($this->_required == FALSE) {
            
            if ($value instanceof Collection && $value->count() == 0) return TRUE;
            else if ($value === NULL) return TRUE;
            
        } else if ((empty($value) && $value !== "0" && $value !== 0) || ($value instanceof Collection && $value->count() == 0)) {
            
            // This is required, but the field the control hasn't returned any output so we can
            // assume it isn't required.
            if ($this->output() == "" && $this->_showEmpty == FALSE) return TRUE;
            
            // This is required!
            $this->setError("This field is required.");
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
                    $fail = ($value != NULL && !@date("U", $value));
                    $message = "Please enter a valid date/time.";
                    break;
                
                case "beforenow":
                    $fail = ($value != NULL && (!@date("U", $value) || $value > time()));
                    $message = "Please enter a valid date/time.";
                    break;
                
                case "afternow":
                    $fail = ($value != NULL && (!@date("U", $value) || $value < time()));
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
                $this->setError((isset($opts["message"]) ? $opts["message"] : $message));
                return FALSE;
            }
            
        }
        
        return TRUE;
    }
}
