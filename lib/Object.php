<?php
/**
 * Object class
 *
 * @package core
 */

/**
 * Object abstract class
 */
abstract class Object
{
    /**
     * Stores the associated collection.
     * 
     * @var  Collection
     */
    protected $_collection;
    
    /**
     * Stores the model's data dictionary of fields, values and validation rules.
     * 
     * @var  array
     */
    protected $_fields = array();
    
    /**
     *
     */
    protected $_customColumns = array();
    
    /**
     *
     */
    protected $_varTypes = array("_fields");
    
    /**
     * Stores the default sort order for a data set.
     *
     * @var  string
     */
    protected $_order = "";
    
    /**
     *
     */
    protected $_uid = "";
    
    /**
     *
     */
    private $__gridHead = array();
    
    /**
     *
     */
    protected $_controls = NULL;
    
    /**
     * Defines a Collection to store a data set of records.
     */
    public function __construct()
    {
        $this->_collection = new Collection($this);
        $this->_collection->setOrder($this->_order);
    }
    
    /**
     * Returns the collection (data set of records)
     * @return  Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }
    
    /**
     * Returns the first record from a data set by filtering by ID.
     * @return  Object|boolean  Returns a model, or FALSE if the ID isn't found.
     */
    public function fetchById($id)
    {
        $this->_collection->setLimit($this->_uid, "=", $id);
        return $this->_collection->fetchFirst();
    }
    
    /**
     *
     */
    public function getFieldHeading($field)
    {
        if (isset($this->_fields[$field]) && ($spec = $this->_fields[$field])) {
            return (isset($spec["heading"]) ? $spec["heading"] : var2label($field));
        }
        
        return FALSE;
    }
    
    /**
     *
     */
    public function getGridHead()
    {
        if ($this->__gridHead != array()) return $this->__gridHead;
        
        $rtn = array();
        
        foreach ($this->_fields as $fieldName => $fieldSpec) {
            
            if (isset($fieldSpec["on_grid"]) && ($spec = $fieldSpec["on_grid"])) {
                
                $rtn[(int)$spec["position"]] = array(
                    "field"    =>  "cite" . var2func($fieldName),
                    "heading"  =>  $this->getFieldHeading($fieldName)
                );
                
            }
            
        }
        
        foreach ($this->_customColumns as $name => $spec) {
            
            $rtn[(int)$spec["position"]] = array(
                "field"    =>  (isset($spec["method"]) ? $spec["method"] : "cite" . var2func($name)),
                "heading"  =>  (isset($spec["heading"]) ? $spec["heading"] : var2label($name))
            );
            
        }
        
        ksort($rtn);
        
        if ($rtn == array()) {
            
            // Grid hasn't been specified so show all fields.
            
            foreach ($this->_fields as $fieldName => $fieldSpec) {
                
                $rtn[] = array(
                    "field"    =>  "cite" . var2func($fieldName),
                    "heading"  =>  var2label($fieldName)
                );
                
            }
            
        }
        
        return $this->__gridHead = $rtn;
    }
    
    /**
     *
     */
    public function uid()
    {
        return $this->{$this->uidField()};
    }
    
    /**
     *
     */
    public function uidField()
    {
        return $this->_uid;
    }
    
    /**
     *
     */
    public function getControls($field=NULL)
    {
        if (!is_array($this->_controls)) {
            
            $rtn = array();
            
            foreach ($this->_varTypes as $varType) {
                
                foreach ($this->$varType as $fieldName => $fieldSpec) {
                    
                    if (isset($fieldSpec["on_edit"]) && ($spec = $fieldSpec["on_edit"])) {
                        
                        $controlClass = $fieldSpec["on_edit"]["control"] . "Control";
                        $control = new $controlClass(
                            $this,
                            $fieldName,
                            $fieldSpec
                        );
                        
                        $rtn[(int)$spec["position"]] = $control;
                        
                    }
                    
                }
                
            }
            
            ksort($rtn);
            $this->_controls = $rtn;
            
        }
        
        if ($field == NULL) return $this->_controls;
        else {
            
            foreach ($this->_controls as $control) {
                if ($control->getVar() == $field) return $control;
            }
            
            return FALSE;
            
        }
    }
    
    /**
     *
     */
    public function validate()
    {
        $errors = 0;
        
        foreach ($this->getControls() as $control) {
            $errors += ($control->validate() ? 0 : 1);
        }
        
        return ($errors == 0);
    }
    
    /**
     * Method overloading handler
     *
     * @param  string  $name  Method called.
     * @param  array  $arguments  Enumerated array of the parameters passed.
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $req = $name;
        
        switch (TRUE) {
            
            case (substr($name, 0, 3) == "get"):
                
                $name = func2var(substr($name, 3));
                
                foreach ($this->_varTypes as $varType) {
                    if (array_key_exists($name, $this->$varType)) {
                        $vars =& $this->$varType;
                        return $vars[$name]["value"];
                    }
                }
                break;
            
            case (substr($name, 0, 4) == "cite"):
                
                $name = func2var(substr($name, 4));
                
                $func = "get" . $name; $value = $this->$func();
                
                switch ($this->typeOf($name)) {
                    
                    case "string":
                        return htmlentities($value);
                    
                    case "text":
                        return nl2br($value);
                    
                    case "timestamp":
                        $pieces = explode(":", $this->_fields[$name]["type"], 2);
                        if (isset($pieces[1])) return date($pieces[1], $value);
                        return date("jS F Y H:i", $value);
                    
                    case "boolean":
                        // do nothing - template handles boolean values
                    
                    case "object":
                        if ($value instanceof Object) {
                            return $value->cite();
                        }
                    
                }
                
                return $value;
            
            case (substr($name, 0, 3) == "set"):
                
                if (isset($arguments[0]) || is_null($arguments[0])) {
                    
                    $name = func2var(substr($name, 3));
                    
                    foreach ($this->_varTypes as $varType) {
                        if (array_key_exists($name, $this->$varType)) {
                            
                            $vars =& $this->$varType;
                            $vars[$name]["value"] = $arguments[0];
                            
                            return TRUE;
                            
                        }
                    }
                    
                }
                
                return FALSE;
            
        }
        
        trigger_error("Call to undefined function " . htmlentities($req) . "()", E_USER_ERROR);
    }
    
    /**
     *
     */
    public function typeOf($var)
    {
        if (!array_key_exists($var, $this->_fields)) return NULL;
        if (!isset($this->_fields[$var]["type"])) return "string";
        
        $pieces = explode(":", $this->_fields[$var]["type"], 2);
        return $pieces[0];
    }
    
    /**
     *
     */
    public function cite()
    {
        return htmlentities($this->_fields[$this->_cite]["value"]);
    }
    
    /**
     *
     */
    abstract public function save();
    
    /**
     *
     */
    abstract public function delete();
    
    /**
     * Variable reading overload handler - uses the __call() method to fetch a variable within
     * the $this->_fields array.
     *
     * @param  string  $name  Name of the property.
     * @return  mixed
     */
    public function __get($name)
    {
        foreach ($this->_varTypes as $varType) {
            if (array_key_exists($name, $this->$varType)) {
                $func = "get" . var2func($name);
                return $this->$func();
            }
        }
        
        return FALSE;
    }
    
    /**
     * Variable writing overload handler - uses the __call() method to write to a variable within
     * the $this->_fields array.
     *
     * @param  string  $name  Name of the property.
     * @return  void  PHP will ignore a returned value.
     */
    public function __set($name, $value)
    {
        foreach ($this->_varTypes as $varType) {
            if (array_key_exists($name, $this->$varType)) {
                $func = "set" . var2func($name);
                $this->$func($value);
            }
        }
    }
}