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
            "type" => "object:Post",
            "model" => array("Post" => "Invalid post identifier."),
            "on_grid" => array("position" => 2, "heading" => "Post")
        ),
        "received" => array(
            "value" => NULL,
            "type" => "timestamp",
            "data_type" => array("timestamp" => "Please enter a valid time."),
            "has_default" => TRUE,
            "on_grid" => array("position" => 1, "heading" => "Received")
        ),
        "content" => array(
            "value" => "",
            "type" => "text",
            "on_grid" => array("position" => 3, "heading" => "Comment")
        )
    );
    
    protected $_order = "received";
}
