<?php
/**
 * MySQL Object
 *
 * @package core
 */

/**
 * MySQL Object abstract class
 */
abstract class MySqlObject extends Object
{
    /**
     * Stores the table name for the model.
     * 
     * @var  string|null
     */
    protected $_table = NULL;
    
    /**
     * Stores the field relationships as a multi-dimensional array in the format
     * var_name => array(column, table, foreign, collection, primary). To call the collection use
     * the function name of var_name (e.g. getVarName()) - the key values are defined as this SQL:
     * SELECT {column} FROM {table} WHERE {foreign} = {primary}. The collection value should
     * be the name of the model to return.
     *
     * @var  array
     */
    protected $_relationships = array();
    
    /**
     * Defines a MySqlCollection to store a data set of records.
     */
    public function __construct()
    {
        $this->_collection = new MySqlCollection($this);
        $this->_collection->setOrder($this->_order);
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
    public function getEditForm()
    {
        $rtn = array();
        
        $varTypes = array("_fields", "_relationships");
        
        foreach ($varTypes as $varType) {
            
            foreach ($this->$varType as $fieldName => $fieldSpec) {
                
                if (isset($fieldSpec["on_edit"]) && ($spec = $fieldSpec["on_edit"])) {
                    
                    $controlClass = $fieldSpec["on_edit"]["control"] . "Control";
                    $control = new $controlClass(
                        $this,
                        $fieldName,
                        $fieldSpec["heading"],
                        (isset($fieldSpec["on_edit"]["tip"]) ? $fieldSpec["on_edit"]["tip"] : NULL)
                    );
                    
                    $rtn[(int)$spec["position"]] = $control;
                    
                }
                
            }
            
        }
        
        ksort($rtn);
        
        return $rtn;
    }
    
    /**
     * Method overloading handler
     *
     * @param  string  $name  Method called.
     * @param  array  $arguments  Enumerated array of the parameters passed.
     * @return mixed
     */
    public function __call($func, $arguments)
    {
        if (substr($func, 0, 3) == "get") {
            
            $fieldName = func2var(substr($func, 3));
            
            if (isset($this->_fields[$fieldName]) && ($fieldSpec = $this->_fields[$fieldName])) {
                
                $value = parent::__call($func, array());
                
                //TODO: only do this if the type is wrong.
                
                switch ($this->typeOf($fieldName)) {
                    
                    case "timestamp":
                        if (!is_int($value)) return $this->$fieldName = strtotime($value);
                        break;
                    
                    case "boolean":
                        if (!is_bool($value)) return $this->$fieldName = ($value == 1);
                        break;
                    
                    case "object":
                        $pieces = explode(":", $this->_fields[$fieldName]["type"], 2);
                        if (isset($pieces[1])) {
                            $modelObject = $pieces[1] . "Object";
                            if (!$value instanceof $modelObject) {
                                $model = new $modelObject();
                                return $this->$fieldName = $model->fetchById($value);
                            }
                        }
                        break;
                }
                
            }
            
            if (isset($this->_relationships[$fieldName]) && ($linkSpec = $this->_relationships[$fieldName])) {
                
                $obj = $linkSpec["collection"];
                $tags = new $obj();
                
                $dbh = MySqlDatabase::getInstance();
                
                $sql = sprintf("SELECT %s FROM %s WHERE %s = ?",
                    $linkSpec["column"],
                    $linkSpec["table"],
                    $linkSpec["foreign"]
                );
                
                if ($sth = $dbh->prepare($sql)) {
                    
                    $func = "get" . var2func($linkSpec["primary"]);
                    
                    try {
                        $sth->execute(array($this->$func()));
                    } catch (PDOException $e) {
                        exit('Database error: ' . $e->getMessage() . " [$sql]");
                    }
                    
                    $tagIds = $sth->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE, 0);
                    
                    if (count($tagIds) > 0 && $tags->getCollection()->setLimit("id", "IN", $tagIds)) {
                        $tags->getCollection()->fetchAll();
                    }
                    
                }
                
                return $tags->getCollection();
                
            }
            
        }
        
        return parent::__call($func, $arguments);
    }
}
