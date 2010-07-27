<?php
/**
 *
 */

/**
 *
 */
class TagObject extends MySqlObject
{
    protected $_table = "tags";
    
    protected $_fields = array(
        "id" => array(
            "value" =>  NULL
        ),
        "tag" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array("test" => "/^.{1,255}$/i", "message" => "Please enter a valid tag."),
                "unique" => array("message" => "Another tag exists with this name.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 1, "heading" => "Tag"),
            "on_edit" => array(
                "position" => 1,
                "control" => "Input"
            )
        )
    );
    
    protected $_relationships = array(
        "posts" => array(
            "type" => "m-m",
            "column" => "post",
            "table" => "post_tags",
            "foreign" => "tag",
            "primary" => "id",
            "collection" => "PostObject"
        )
    );
    
    protected $_order = "tag";
    protected $_cite = "tag";
    protected $_uid = "id";
}
