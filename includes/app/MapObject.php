<?php
/**
 * Map Object
 *
 * @package core
 */

/**
 * Map Object abstract class
 */
abstract class MapObject extends Object
{
    /**
     *
     */
    protected $_values = array();
    
    /**
     * Defines an ArrayCollection to store a data set of records.
     */
    public function __construct()
    {
        $this->_collection = new MapCollection($this);
        $this->_collection->setOrder($this->_order);
    }
    
    /**
     *
     */
    public function getValues()
    {
        return $this->_values;
    }
}
