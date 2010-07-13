<?php
/**
 *
 */

/**
 *
 */
class CommentObject extends MySqlObject
{
    protected $_table = "comments";
    
    protected $_fields = array(
        "id" => array(
            "value" =>  NULL,
            "data_type" => array("primary" => "Invalid comment identifier.")
        ),
        "post" => array(
            "value" => "",
            "model" => array("Post" => "Invalid post identifier.")
        ),
        "received" => array(
            "value" => NULL,
            "data_type" => array("timestamp" => "Please enter a valid time."),
            "has_default" => TRUE
        ),
        "content" => array(
            "value" => ""
        )
    );
    
    protected $_order = "received";
}
