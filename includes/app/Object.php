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
    private $__grid_head = array();
    
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
        $this->_collection->setLimit("id", "=", $id);
        return $this->_collection->fetchFirst();
    }
    
    /**
     *
     */
    public function getGridHead()
    {
        if ($this->__grid_head != array()) return $this->__grid_head;
        
        $rtn = array();
        
        foreach ($this->_fields as $fieldName => $fieldSpec) {
            
            if (isset($fieldSpec["on_grid"]) && ($spec = $fieldSpec["on_grid"])) {
                
                $rtn[(int)$spec["position"]] = array(
                    "field"    =>  "cite" . var2func($fieldName),
                    "heading"  =>  (isset($fieldSpec["heading"]) ? $fieldSpec["heading"] : var2label($fieldName))
                );
                
            }
            
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
        
        return $this->__grid_head = $rtn;
    }
    
    /**
     *
     */
    public function uid()
    {
        return $this->{$this->_uid};
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
                
                if (array_key_exists($name, $this->_fields)) {
                    return $this->_fields[$name]["value"];
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
                
                $name = func2var(substr($name, 3));
                if (array_key_exists($name, $this->_fields)) {
                    if (isset($arguments[0])) {
                        $this->_fields[$name]["value"] = $arguments[0];
                        return TRUE;
                    }
                    return FALSE;
                }
                break;
            
        }
        
        trigger_error("Call to undefined function " . htmlentities($req) . "()", E_USER_ERROR);
    }
    
    /**
     *
     */
    protected function typeOf($var)
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
     * Variable reading overload handler - uses the __call() method to fetch a variable within
     * the $this->_fields array.
     *
     * @param  string  $name  Name of the property.
     * @return  mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_fields)) {
            $func = "get" . var2func($name);
            return $this->$func();
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
        if (array_key_exists($name, $this->_fields)) {
            $func = "set" . var2func($name);
            $this->$func($value);
        }
    }
}
