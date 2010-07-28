<?php
/**
 *
 */

/**
 *
 */
class TopicObject extends MySqlObject
{
    protected $_table = "topics";
    
    protected $_fields = array(
        "id" => array(
            "value" =>  NULL
        ),
        "topic" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array("test" => "/^.{1,255}$/i", "message" => "Please enter a valid topic."),
                "unique" => array("message" => "Another topc exists with this name.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 1),
            "on_edit" => array(
                "position" => 1,
                "control" => "Input"
            )
        )
    );
    
    protected $_relationships = array(
        "posts" => array(
            "type" => "1-m",
            "value" => NULL,
            "column" => "id",
            "table" => "posts",
            "foreign" => "topic",
            "primary" => "id",
            "collection" => "PostObject"
        )
    );
    
    protected $_order = "topic";
    protected $_cite = "topic";
    protected $_uid = "id";
}
