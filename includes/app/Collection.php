<?php
/**
 * Collection
 *
 * @package core
 */

/**
 * This class implements the iterator interface so that we can easily foreach() through the
 * data set.
 */
abstract class Collection implements Iterator
{
    /**
     * Stores the current data set.
     * 
     * @var  array
     */
    protected $_dataSet = array();
    
    /**
     * Stores the limits put in place using the setLimit() method.
     *
     * @var  array
     */
    protected $_limits = array("conditions" => array(), "values" => array());
    
    /**
     * Stores the record set offset.
     * 
     * @var  int|null
     */
    protected $_start = NULL;
    
    /**
     * Stores the maximum number of records to return.
     * 
     * @var  int|null
     */
    protected $_range = NULL;
    
    /**
     * Stores the default sort order.
     *
     * @var  string
     */
    protected $_order = "";
    
    /**
     * Stores the total number of records in the record set.
     * 
     * @var  int
     */
    protected $_total = 0;
    
    /**
     * Constructor.
     *
     * @param  Object  $obj  Specify what model the collection is for.
     */
    public function __construct(Object $obj)
    {
        $this->_obj = $obj;
    }
    
    /**
     * Limits the records returned when the collection is fetched.
     *
     * @param  string  $field  The field name to apply the limit to.
     * @param  string  $condition  The condition to limit by. The condition isn't validated so
     *                             anything can be specified. Typical value conditions would be
     *                             =, !=, <, >, IN or NOT IN. If IN or NOT IN are specified the
     *                             $value should be an array.
     * @param  string|int|float|array  $value  The value to limit by.
     * @return  boolean
     */
    public function setLimit($field, $condition, $value)
    {
        if ((strtoupper($condition) == "IN" || strtoupper($condition) == "NOT IN") && is_array($value)) {
            
            if (count($value) == 0) return FALSE;
            
            $this->_limits["conditions"][] = $field . " " . strtoupper($condition) .
            " (" . substr(str_repeat("?, ", count($value)), 0, -strlen(", ")) . ")";
            
            foreach ($value as $inValue) $this->_limits["values"][] = $inValue;
            
        } else {
            
            $this->_limits["conditions"][] = $field . " " . $condition . " ?";
            $this->_limits["values"][] = $value;
            
        }
        
        return TRUE;
    }
    
    /**
     * Defined the offset and maximum number of records to return.
     *
     * @param  int  $start  The offset.
     * @param  int  $range  The maximum number of records to return.
     * @return void
     */
    public function setPagination($start, $range)
    {
        $this->_start = (int)$start;
        $this->_range = (int)$range;
    }
    
    /**
     * Sets the sort order.
     *
     * @param  string  $order
     */
    public function setOrder($order)
    {
        $this->_order = $order;
    }
    
    /**
     * Fetches the record set and returns the first record.
     *
     * @return  object
     */
    public function fetchFirst()
    {
        $this->fetchAll();
        return reset($this->_dataSet);
    }
    
    /**
     * Should fetch and return the data set.
     */
    abstract public function fetchAll();
    
    /**
     * Returns the current record.
     *
     * @return  object
     */
    public function current()
    {
        return current($this->_dataSet);
    }
    
    /**
     * Returns the key of the current record.
     *
     * @return  scalar|null
     */
    public function key()
    {
        return key($this->_dataSet);
    }
    
    /**
     * Moves to the next record.
     *
     * @return  void
     */
    public function next()
    {
        next($this->_dataSet);
    }
    
    /**
     * Rewinds the iterator to the first record.
     *
     * @return  void
     */
    public function rewind()
    {
        reset($this->_dataSet);
    }
    
    /**
     * Checks if the current position is valid.
     *
     * @return  boolean
     */
    public function valid()
    {
        return ($this->current() !== FALSE);
    }
    
    /**
     * Returns the number of records in the data set. Smarty doesn't support using count($obj) on
     * iterators so this is a workaround for that.
     *
     * @return  int
     */
    public function count()
    {
        return count($this->_dataSet);
    }
}
