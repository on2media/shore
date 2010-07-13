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
            "data_type" => array("timestamp" => "Please enter a valid time."),
            "has_default" => TRUE
        ),
        "author" => array(
            "value" => "",
            "model" => array("Author" => "Invalid author identifier.")
        ),
        "title" => array(
            "value" => "",
            "regexp" => array("/^.{1,255}$/i" => "Please enter a valid title.")
        ),
        "content" => array(
            "value" => ""
        ),
        "can_comment" => array(
            "value" => NULL,
            "data_type" => array("boolean" => "Please select yes or no."),
            "has_default" => TRUE
        ),
        "type" => array(
            "value" => "",
            "regexp" => array("/^post|page$/" => "Is this a page or a post?")
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
}
