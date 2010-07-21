<?php
/**
 *
 */

/**
 *
 */
class PostTypeObject extends MapObject
{
    protected $_fields = array(
        "key" => array("value" =>  NULL),
        "value" => array("value" => NULL)
    );
    
    protected $_values = array(
        array(
            "key"    =>  "post",
            "value"  =>  "Standard Post"
        ),
        array(
            "key"    =>  "page",
            "value"  =>  "Blog Page"
        )
    );
    
    protected $_order = "key";
    protected $_cite = "value";
    protected $_uid = "key";
}
