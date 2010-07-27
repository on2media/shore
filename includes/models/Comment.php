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
            "value" =>  NULL
        ),
        "post" => array(
            "value" => "",
            "type" => "object:Post",
            "validation" => array(
                "object" => array("object" => "Post", "message" => "A comment must refer to a post.")
            ),
            "required" => TRUE,
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
            "validation" => array(
                "timestamp" => array("message" => "Please enter a valid time")
            ),
            "required" => TRUE,
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
            "validation" => array(
                "regexp" => array("test" => "/^.+$/im", "message" => "Please enter a comment.")
            ),
            "required" => TRUE,
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
