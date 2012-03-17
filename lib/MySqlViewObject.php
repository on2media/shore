<?php
/**
 * MySQL View Object
 *
 * @package core
 */

/**
 * MySQL View Object abstract class
 */
abstract class MySqlViewObject extends MySqlObject
{
    /**
     * Stores the table name for the model.
     * 
     * @var  string|null
     */
    protected $_table = NULL;
    
    /**
     *
     */
    protected $_varTypes = array("_fields");
    
    /**
     * Defines a MySqlCollection to store a data set of records.
     */
    public function __construct()
    {
        $this->_collection = new MySqlCollection($this);
        $this->init();
    }
    
    /**
     * Returns the MySQL table name.
     * @return  string
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     *
     */
    public function save()
    {
        throw new Exception("MySQL View Objects can't be created or updated.");
    }
    
    /**
     *
     */
    public function delete()
    {
        throw new Exception("MySQL View Objects can't be deleted.");
    }
    
    /**
     *
     */
    public function decryptCollection() {}
    
    /**
     *
     */
    public function isNew()
    {
        return FALSE;
    }
}
