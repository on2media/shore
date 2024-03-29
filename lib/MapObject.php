<?php
/**
 * Map Object
 *
 * @package core
 */

/**
 * Map Object abstract class
 */
abstract class MapObject extends ShoreObject
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
        $this->init();
    }

    /**
     *
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     *
     */
    public function save($inTransaction=FALSE)
    {
        throw new Exception("Map Objects can't be created or updated.");
    }

    /**
     *
     */
    public function delete()
    {
        throw new Exception("Map Objects can't be deleted.");
    }
}
