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
     * @var string|null
     */
    protected $_table = NULL;

    /**
     *
     */
    protected $_gridrel = "";

    /**
     * Denotes array key names that point to
     * model properties. Used in xxxxObject classes
     *
     * @var array
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
    public function getGridRel()
    {
        return $this->_gridrel;
    }

    /**
     * Save method for model objects
     *
     * @param bool $inTransaction
     * @return bool|int Boolean or last insert id
     */
    public function save($inTransaction=FALSE)
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
     * Check if object is new, i.e. for updating or inserting
     *
     * @param bool $new|null
     * @return bool
     */
    public function isNew($new=NULL)
    {
        return FALSE;
    }
}
