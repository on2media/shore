<?php
/**
 *
 */

/**
 *
 */
class XmlView extends View
{
    /**
     *
     */
    protected $_xml = array();

    /**
     *
     */
    public function __construct(DOMDocument $xml)
    {
        @header("Content-Type: text/xml;charset=utf-8");
        $this->_xml = $xml;
    }

    /**
     *
     */
    public function output()
    {
        return $this->_xml->saveXML();
    }
}
