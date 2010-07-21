<?php
/**
 * Map Collection
 *
 * @package core
 */

/**
 * Extends the Collection class and handles requests to array-based (map) models.
 */
class MapCollection extends Collection
{
    /**
     *
     */
    protected $_limits = array();
    
    /**
     * Calls the parent constructor, but forces the model to be a MapObject by utilising type
     * hinting.
     */
    public function __construct(MapObject $obj)
    {
        parent::__construct($obj);
    }
    
    /**
     *
     */
    public function setLimit($field, $condition, $value)
    {
        $this->_limits[] = array(
            "field" => $field,
            "condition" => $condition,
            "value" => $value
        );
    }
    
    public function fetchAll()
    {
        //TODO: Handle... $this->_start   $this->_range   $this->_order
        
        $objClass = get_class($this->_obj);
        
        foreach ($this->_obj->getValues() as $data) {
            
            $obj = new $objClass();
            foreach ($data as $key => $value) $obj->$key = $value;
            $this->_dataSet[$obj->uid()] = $obj;
            
        }
        
        foreach ($this->_limits as $limit) {
            
            foreach ($this as $key => $obj) {
                
                $remove = FALSE;
                
                switch (strtoupper($limit["condition"])) {
                    
                    case "=":
                        $remove = ($obj->$limit["field"] != $limit["value"]);
                        break;
                    
                    case "!=":
                    case "<>":
                        $remove = ($obj->$limit["field"] == $limit["value"]);
                        break;
                    
                    case "IN":
                        //TODO
                        break;
                    
                    case "NOT IN":
                        //TODO
                        break;
                    
                }
                
                if ($remove) unset($this->_dataSet[$key]);
                
            }
            
        }
        
        $this->_total = count($this->_dataSet);
        
        return $this;
    }
}
