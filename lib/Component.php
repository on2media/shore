<?php
/**
 *
 */

/**
 *
 */
abstract class Component {

	/**
	 *
	 * @var unknown_type
	 */
	protected $_viewTemplateName = '';

    /**
     *
     */
    protected $_controller = NULL;

    /**
     *
     * @var unknown_type
     */
    protected $_messages = array();

    /**
     *
     */
    public function __construct(Controller $controller)
    {
        $this->_controller = $controller;
    }

    public function getMessages() {
    	return $this->_messages;
    }

    public function getView() {
    	return $this->_viewTemplateName;
    }

    public function setView($name) {
    	$this->_viewTemplateName = $name;
    }
}
