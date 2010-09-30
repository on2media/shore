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
     *
     */
    protected $_varTypes = array("_fields", "_relationships");
    
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
    public function save($inTransaction=FALSE)
    {
        if (!$this->validate()) return FALSE;
        
        $dbh = MySqlDatabase::getInstance();
        
        $fields = $values = array();
        
        foreach($this->_fields as $fieldName => $fieldSpec) {
            
            if ($fieldName != $this->uidField()) {
                
                $fields[] = $fieldName;
                $value = $this->$fieldName;
                
                switch ($this->typeOf($fieldName)) {
                    
                    case "boolean":
                        
                        $value = ($value == TRUE ? 1 : 0);
                        break;
                    
                    case "object":
                        
                        $value = ($value != NULL ? $value->uid() : NULL);
                        break;
                    
                    case "timestamp":
                        
                        $value = ($value != NULL ? date("Y-m-d H:i:s", $value) : NULL);
                        break;
                    
                }
                
                if (!isset($fieldSpec["not_null"]) || $fieldSpec["not_null"] != TRUE) {
                    if ($value === "") $value = NULL;
                }
                
                if (isset($fieldSpec["encrypt"]) && $fieldSpec["encrypt"] == TRUE) {
                    $values[] = base64_encode(mcrypt_encrypt(
                        MCRYPT_RIJNDAEL_128, ENCRYPTION_SALT, $value, MCRYPT_MODE_ECB
                    ));
                } else {
                    $values[] = $value;
                }
                
            }
            
        }
        
        $forceInsert = FALSE;
        
        if ($this->{$this->uidField()} instanceof MySqlObject) {
            
            $objType = get_class($this);
            
            $search = new $objType();
            if (!$search->fetchById($this->uid())) {
                
                $fields[] = $this->uidField();
                $values[] = $this->uid();
                $forceInsert = TRUE;
                
            }
            
        }
        
        if (!$forceInsert && $this->uid() != NULL) {
            
            $sql = sprintf("UPDATE %s SET ", $this->quoteField($this->_table));
            foreach ($fields as $field) $sql .= $this->quoteField($field) . "=?, ";
            $sql = substr($sql, 0, -strlen(", ")) . sprintf(" WHERE %s=?", $this->uidField());
            
            $values[] = $this->uid();
            
        } else {
            
            $sql = sprintf("INSERT INTO %s (", $this->quoteField($this->_table));
            foreach ($fields as $field) $sql .= $this->quoteField($field) . ", ";
            $sql = substr($sql, 0, -strlen(", ")) . ") VALUES (";
            for ($i=0;$i<count($values);$i++) $sql .= "?, ";
            $sql = substr($sql, 0, -strlen(", ")) . ")";
            
        }
        
        $sth = $dbh->prepare($sql);
        
        try {
            if (!$sth->execute($values)) return FALSE;
        } catch (PDOException $e) {
            exit('Database error: ' . $e->getMessage());
        }
        
        if ($this->uid() == NULL) $this->{$this->uidField()} = $dbh->lastInsertId();
        
        foreach($this->_relationships as $fieldName => $fieldSpec) {
            
            if ($fieldSpec["type"] == "m-m" || ($fieldSpec["type"] == "1-m" && isset($fieldSpec["on_edit"]))) {
                
                try {
                    
                    if ($inTransaction || $dbh->beginTransaction()) {
                        
                        $sth = $dbh->prepare(sprintf("DELETE FROM %s WHERE %s=?",
                            $this->quoteField($fieldSpec["table"]),
                            $this->quoteField($fieldSpec["foreign"])
                        ));
                        
                        if (!$sth->execute(array($this->uid()))) {
                            
                            if (!$inTransaction) $dbh->rollBack();
                            return FALSE;
                            
                        }
                        
                    }
                    
                } catch (PDOException $e) {
                    exit('Database error: ' . $e->getMessage());
                }
                
                if ($fieldSpec["type"] == "m-m") {
                    
                    $fieldValue = $this->$fieldName;
                    
                    if (is_object($fieldValue) && $fieldValue instanceof Collection) {
                        
                        try {
                            
                            foreach ($fieldValue as $obj) {
                                
                                $sth = $dbh->prepare(sprintf("INSERT INTO %s (%s, %s) VALUES (?, ?)",
                                    $this->quoteField($fieldSpec["table"]),
                                    $this->quoteField($fieldSpec["foreign"]),
                                    $this->quoteField($fieldSpec["column"])
                                ));
                                
                                if (!$sth->execute(array($this->uid(), $obj->uid()))) {
                                    
                                    if (!$inTransaction) $dbh->rollBack();
                                    return FALSE;
                                    
                                }
                                
                            }
                            
                            if (!$inTransaction) $dbh->commit();
                            
                        } catch (PDOException $e) {
                            exit('Database error: ' . $e->getMessage());
                        }
                        
                    }
                    
                } else if ($fieldSpec["type"] == "1-m" && isset($fieldSpec["on_edit"])) {
                    
                    foreach ($this->$fieldName as $obj) {
                        
                        if ($obj instanceof MySqlObject) {
                            
                            if (!$obj->save(TRUE)) {
                                
                                if (!$inTransaction) $dbh->rollBack();
                                return FALSE;
                                
                            }
                            
                        }
                        
                    }
                    
                    if (!$inTransaction) $dbh->commit();
                    
                }
                
            }
            
        }
        
        return TRUE;
    }
    
    /**
     *
     */
    public function delete()
    {
        $dbh = MySqlDatabase::getInstance();
        
        $sql = sprintf("DELETE FROM %s WHERE %s=?", $this->_table, $this->uidField());
        $sth = $dbh->prepare($sql);
        
        try {
            if (!$sth->execute(array($this->uid()))) return FALSE;
        } catch (PDOException $e) {
            return FALSE;
        }
        
        return TRUE;
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
                            
                            if ($value == NULL || $value instanceof $modelObject) {
                                $rtn = $value;
                            } else {
                                $model = new $modelObject();
                                $rtn = $model->fetchById($value);
                            }
                            
                            return $this->$fieldName = ($rtn ? $rtn : NULL);
                            
                        }
                        break;
                }
                
            }
            
            if (isset($this->_relationships[$fieldName]) && ($linkSpec = $this->_relationships[$fieldName])) {
                
                $obj = $linkSpec["collection"];
                $items = new $obj();
                
                $value =& $this->_relationships[$fieldName]["value"];
                
                if ($value instanceof Collection && $value->getObject() instanceof $obj) return $value;
                
                $dbh = MySqlDatabase::getInstance();
                
                $sql = sprintf("SELECT %s FROM %s WHERE %s = ?",
                    $this->quoteField($linkSpec["column"]),
                    $this->quoteField($linkSpec["table"]),
                    $this->quoteField($linkSpec["foreign"])
                );
                
                if ($sth = $dbh->prepare($sql)) {
                    
                    $func = "get" . var2func($linkSpec["primary"]);
                    
                    $value = $this->$func();
                    if ($this->$func() instanceof MySqlObject) $value = $value->uid();
                    
                    try {
                        $sth->execute(array($value));
                    } catch (PDOException $e) {
                        exit('Database error: ' . $e->getMessage() . " [$sql]");
                    }
                    
                    $objIds = $sth->fetchAll(PDO::FETCH_COLUMN|PDO::FETCH_UNIQUE, 0);
                    
                    if (count($objIds) == 0 || !$items->getCollection()->setLimit($items->uidField(), "IN", $objIds)) {
                        $items->getCollection()->setLimit($items->uidField(), "=", NULL);
                    }
                    
                    if ($this->_relationships[$fieldName]["type"] == "1-1") {
                        return $items->getCollection()->fetchFirst();
                    }
                    
                    // if FALSE is passed as the first argument then do not fetch the entire
                    // collection and do not set the value in the class.
                    
                    if (isset($arguments[0]) && $arguments[0] === FALSE) {
                        return $items->getCollection();
                    }
                    
                    $items->getCollection()->fetchAll();
                    
                }
                
                return $value = $items->getCollection();
                
            }
            
        }
        
        return parent::__call($func, $arguments);
    }
    
    /**
     *
     */
    protected function quoteField($field)
    {
        return (substr($field, 0, 1) == "*" ? substr($field, 1) : "`" . $field . "`");
    }
    
    /**
     *
     */
    public function decryptCollection()
    {
        foreach ($this->_fields as $fieldName => $fieldSpec) {
            
            if (isset($fieldSpec["encrypt"]) && $fieldSpec["encrypt"] == TRUE) {
                
                foreach ($this->_collection as $obj) {
                    $obj->$fieldName = trim(mcrypt_decrypt(
                        MCRYPT_RIJNDAEL_128, ENCRYPTION_SALT, base64_decode($obj->$fieldName), MCRYPT_MODE_ECB
                    ));
                }
                
            }
            
        }
    }
}
