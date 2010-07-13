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
     * Method overloading handler
     *
     * @param  string  $name  Method called.
     * @param  array  $arguments  Enumerated array of the parameters passed.
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $req = $name;
        
        switch (substr($name, 0, 3)) {
            
            case "get":
                
                // get the variable name from the function name
                $name = func2var(substr($name, 3));
                
                // this is a valid field
                if (array_key_exists($name, $this->_fields)) {
                    
                    $field =& $this->_fields[$name];
                    $value =& $field["value"];
                    
                    return $value;
                }
            
            case "set":
                
                $name = func2var(substr($name, 3));
                if (array_key_exists($name, $this->_fields)) {
                    if (isset($arguments[0])) {
                        $this->_fields[$name]["value"] = $arguments[0];
                        return TRUE;
                    }
                    return FALSE;
                }
            
        }
        
        trigger_error("Call to undefined function " . htmlentities($req) . "()", E_USER_ERROR);
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
