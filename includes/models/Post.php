<?php
/**
 *
 */

/**
 *
 */
class PostObject extends MySqlObject
{
    protected $_table = "posts";
    
    protected $_fields = array(
        "id" => array(
            "value" =>  NULL,
            "data_type" => array("primary" => "Invalid post identifier.")
        ),
        "posted" => array(
            "value" => NULL,
            "type" => "timestamp:jS F Y \\a\\t g:ia",
            "validation" => array(
                "rule" => "timestamp",
                "required" => TRUE,
                "message" => "Please enter a valid time"
            ),
            "data_type" => array("timestamp" => "Please enter a valid time."),
            "has_default" => TRUE,
            "on_grid" => array("position" => 1, "heading" => "Posted")
        ),
        "author" => array(
            "value" => "",
            "type" => "object:Author",
            "model" => array("Author" => "Invalid author identifier."),
            "on_grid" => array("position" => 4, "heading" => "Author")
        ),
        "title" => array(
            "value" => "",
            "type" => "string",
            "regexp" => array("/^.{1,255}$/i" => "Please enter a valid title."),
            "on_grid" => array("position" => 3, "heading" => "Title")
        ),
        "content" => array(
            "value" => ""
        ),
        "can_comment" => array(
            "value" => NULL,
            "type" => "boolean",
            "data_type" => array("boolean" => "Please select yes or no."),
            "has_default" => TRUE,
            "on_grid" => array("position" => 5, "heading" => "Can Comment?")
        ),
        "type" => array(
            "value" => "",
            "regexp" => array("/^post|page$/" => "Is this a page or a post?"),
            "on_grid" => array("position" => 2, "heading" => "Page or Post?")
        ),
    );
    
    protected $_relationships = array(
        "tags" => array(
            "column" => "tag",
            "table" => "post_tags",
            "foreign" => "post",
            "primary" => "id",
            "collection" => "TagObject"
        ),
        "comments" => array(
            "column" => "id",
            "table" => "comments",
            "foreign" => "post",
            "primary" => "id",
            "collection" => "CommentObject"
        )
    );
    
    protected $_order = "posted DESC";
    
    protected $_cite = "title";
}
