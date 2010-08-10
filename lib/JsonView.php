<?php
/**
 *
 */

/**
 * 
 */
class JsonView extends View
{
    /**
     *
     */
    protected $_json = array();
    
    /**
     *
     */
    public function __construct($data)
    {
        @header("Content-Type: application/json");
        $this->_json = $data;
    }
    
    /**
     *
     */
    public function output(array $data=array())
    {
        return json_encode($this->_json);
    }
}
