<?php
/**
 *
 */

/**
 *
 */
class SessionObject extends MySqlObject
{
    protected $_table = "session";
    
    protected $_fields = array(
        "id" => array(
            "value" => NULL
        ),
        "data" => array(
            "value" => "",
            "lob" => TRUE,
            "not_null" => TRUE,
            "type" => "text"
        ),
        "last_modified" => array(
            "value" => "",
            "type" => "timestamp:jS F Y \\a\\t g:ia",
            "validation" => array(
                "timestamp" => array("message" => "Please enter a valid time.")
            )
        )
    );
    
    protected $_order = "id";
    protected $_cite = "id";
    protected $_uid = "id";
}
