<?php
/**
 * MySQL Collection
 *
 * @package core
 */

/**
 * Extends the Collection class and handles requests to MySQL database models.
 */
class MySqlCollection extends Collection
{
    /**
     * Calls the parent constructor, but forces the model to be a MySqlObject by utilising type
     * hinting.
     */
    public function __construct(MySqlObject $obj)
    {
        parent::__construct($obj);
    }
    
    /**
     * Fetches the data set from the MySQL database.
     * @return MySqlCollection
     */
    public function fetchAll()
    {
        $sql = sprintf("SELECT SQL_CALC_FOUND_ROWS '%s' AS PDOclass, id AS PDOid, tbl.* FROM %s AS tbl %s",
            get_class($this->_obj),
            $this->_obj->getTable(),
            $this->limitSql() . $this->getOrder() . $this->getPagination()
        );
        
        return $this->fetchDataSet($sql);
    }
    
    /**
     * Returns the SQL used to filter the data set.
     * @return  string  Will return an empty string if no filters have been set.
     */
    protected function limitSql()
    {
        return (count($this->_limits["conditions"]) > 0 ? " WHERE " . implode(" AND ", $this->_limits["conditions"]) : "");
    }
    
    /**
     * Returns the SQL used to set the offset and limit the number of records to return.
     * @return  string  Will return an empty string if no limits have been set.
     */
    protected function getPagination()
    {
        if ($this->_start !== NULL && $this->_range !== NULL) {
            return sprintf(" LIMIT %d, %d", $this->_start, $this->_range);
        }
        return "";
    }
    
    /**
     * Returns the SQL used to define the sort order.
     *
     * @return  string
     */
    public function getOrder()
    {
        return " ORDER BY " . $this->_order;
    }
    
    /**
     * Fetches the filtered data set and sets the total number of records found.
     * @param  string  $sql  The SQL to execute.
     * @return  MySqlCollection
     */
    protected function fetchDataSet($sql)
    {
        $dbh = MySqlDatabase::getInstance();
        
        if ($sth = $dbh->prepare($sql)) {
            
            try {
                $sth->execute($this->_limits["values"]);
            } catch (PDOException $e) {
                exit('Database error: ' . $e->getMessage() . " [$sql]");
            }
            
            $this->_dataSet = $sth->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_CLASSTYPE|PDO::FETCH_UNIQUE);
            
            if ($sth = $dbh->query("SELECT FOUND_ROWS()")) $this->_total = $sth->fetchColumn(0);
            
        }
        
        return $this;
    }
}
