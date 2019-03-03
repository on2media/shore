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
        @header("Content-Type: application/json;charset=utf-8");
        $this->_json = $data;
    }

    /**
     *
     */
    public function output()
    {
        return json_encode($this->_json);
    }
}
