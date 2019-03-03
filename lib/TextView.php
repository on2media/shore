<?php
/**
 *
 */

/**
 *
 */
class TextView extends View
{
    /**
     *
     */
    protected $_text = array();

    /**
     *
     */
    public function __construct($text)
    {
        @header("Content-Type: text/plain;charset=utf-8");
        $this->_text = $text;
    }

    /**
     *
     */
    public function output()
    {
        return $this->_text;
    }
}
