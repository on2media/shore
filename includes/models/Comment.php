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
            //"data_type" => array("primary" => "Invalid comment identifier.")
        ),
        "post" => array(
            "value" => "",
            "type" => "object:Post",
            //"model" => array("Post" => "Invalid post identifier."),
            "on_grid" => array("position" => 2, "heading" => "Post"),
            "on_edit" => array(
                "position" => 1,
                "control" => "View",
                "tip" => "This comment is in reference to this post."
            )
        ),
        "received" => array(
            "value" => NULL,
            "type" => "timestamp",
            //"data_type" => array("timestamp" => "Please enter a valid time."),
            //"has_default" => TRUE,
            "on_grid" => array("position" => 1, "heading" => "Received"),
            "on_edit" => array(
                "position" => 2,
                "control" => "DateTimePicker",
                "tip" => "Use the date picker to select a date and time."
            )
        ),
        "content" => array(
            "value" => "",
            "type" => "text",
            "on_grid" => array("position" => 3, "heading" => "Comment"),
            "on_edit" => array(
                "position" => 3,
                "control" => "Textarea",
                "tip" => "Edit the comment."
            )
        )
    );
    
    protected $_order = "received";
    protected $_cite = "received";
    protected $_uid = "id";
}
