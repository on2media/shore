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
    protected $_cite = "";
    
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
    protected $_controlPrefix = "";
    
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
    public function getFieldSpec($var)
    {
        return (isset($this->_fields[$var]) ? $this->_fields[$var] : FALSE);
    }
    
    /**
     *
     */
    public function getObjFields()
    {
        return $this->_fields;
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
    public function getFieldFilter($field)
    {
        if (
            isset($this->_fields[$field]) && ($fieldSpec = $this->_fields[$field]) &&
            isset($fieldSpec["on_grid"]) && ($spec = $fieldSpec["on_grid"]) &&
            isset($spec["filter"]) && ($filterType = $spec["filter"])
        ) {
            
            switch ($filterType) {
                
                case "freetext":
                    return array("type" => "freetext");
                
                case "dropdown":
                    if (isset($fieldSpec["type"]) && substr($fieldSpec["type"], 0, strlen("object:")) == "object:") {
                        
                        $options = array();
                        
                        $className = substr($fieldSpec["type"], strlen("object:")) . "Object";
                        
                        $obj = new $className();
                        foreach ($obj->getCollection()->fetchAll() as $obj) {
                            $options[$obj->uid()] = $obj->cite();
                        }
                        
                        return array(
                            "type" => "dropdown",
                            "options" => $options
                        );
                        
                    }
                
            }
            
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
                    "field"     =>  "cite" . var2func($fieldName),
                    "heading"   =>  $this->getFieldHeading($fieldName),
                    "filter"    =>  $this->getFieldFilter($fieldName),
                    "sortable"  =>  (isset($spec["sortable"]) && $spec["sortable"] == TRUE)
                );
                
            }
            
        }
        
        foreach ($this->_customColumns as $name => $spec) {
            
            $rtn[(int)$spec["position"]] = array(
                "field"     =>  (isset($spec["method"]) ? $spec["method"] : "cite" . var2func($name)),
                "heading"   =>  (isset($spec["heading"]) ? $spec["heading"] : var2label($name)),
                "filter"    =>  FALSE,
                "sortable"  =>  FALSE
            );
            
        }
        
        ksort($rtn);
        
        if ($rtn == array()) {
            
            // Grid hasn't been specified so show all fields.
            
            foreach ($this->_fields as $fieldName => $fieldSpec) {
                
                $rtn[] = array(
                    "field"     =>  "cite" . var2func($fieldName),
                    "heading"   =>  var2label($fieldName),
                    "filter"    =>  FALSE,
                    "sortable"  =>  FALSE
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
        $rtn = $this;
        
        do {
            
            if (substr($rtn->uidField(), 0, 1) == "*") return NULL;
            
            $rtn = $rtn->{$rtn->uidField()};
            
        } while ($rtn instanceof Object);
        
        return $rtn;
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
    public function setControlPrefix($prefix)
    {
        if ($this->_controlPrefix == "" && !is_array($this->controls)) {
            $this->_controlPrefix = $prefix;
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     *
     */
    public function deleteControls()
    {
        $this->_controls = NULL;
    }
    
    /**
     *
     */
    public function getControls($field=NULL)
    {
        if (!is_array($this->_controls)) {
            
            $session = Session::getInstance();
            $authModel = AUTH_MODEL;
            $superUser = ($session->getUser() instanceof $authModel && $session->getUser()->getSuperuser());
            
            $rtn = array();
            
            foreach ($this->_varTypes as $varType) {
                
                foreach ($this->$varType as $fieldName => $fieldSpec) {
                    
                    if (isset($fieldSpec["on_edit"]) && ($spec = $fieldSpec["on_edit"])) {
                        
                        $controlClass = FALSE;
                        
                        if ($superUser && array_key_exists("superuser", $fieldSpec["on_edit"])) {
                            $controlClass = $fieldSpec["on_edit"]["superuser"] . "Control";
                        } elseif (array_key_exists("control", $fieldSpec["on_edit"])) {
                            $controlClass = $fieldSpec["on_edit"]["control"] . "Control";
                        }
                        
                        if ($controlClass) {
                            
                            $control = new $controlClass(
                                $this,
                                $this->_controlPrefix,
                                $fieldName,
                                $fieldSpec
                            );
                            
                            $rtn[(int)$spec["position"]] = $control;
                            
                        }
                        
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
                        return htmlspecialchars($value);
                    
                    case "text":
                        return nl2br($value);
                    
                    case "timestamp":
                        if ($value == NULL) return "";
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
        
        throw new Exception("Call to undefined function " . htmlspecialchars($req) . "() in " . htmlspecialchars(get_class($this)));
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
        $rtn = $this->{$this->_cite};
        if ($rtn instanceof Object) $rtn = $rtn->cite();
        
        return htmlspecialchars($rtn);
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
