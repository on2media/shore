<?php
/**
 *
 */

/**
 *
 */
abstract class Component
{
    /**
     *
     */
    protected $_controller = NULL;
    
    /**
     *
     */
    public function __construct(Controller $controller)
    {
        $this->_controller = $controller;
    }
}
