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
            //"data_type" => array("primary" => "Invalid post identifier.")
        ),
        "posted" => array(
            "value" => NULL,
            "type" => "timestamp:jS F Y \\a\\t g:ia",
            "validation" => array(
                "rule" => "timestamp",
                "required" => TRUE,
                "message" => "Please enter a valid time"
            ),
            //"data_type" => array("timestamp" => "Please enter a valid time."),
            //"has_default" => TRUE,
            "on_grid" => array("position" => 1),
            "on_edit" => array(
                "position" => 1,
                "control" => "DateTimePicker",
                "tip" => "Use the date picker to select a date and time."
            )
        ),
        "author" => array(
            "value" => "",
            "type" => "object:Author",
            //"model" => array("Author" => "Invalid author identifier."),
            "on_grid" => array("position" => 4),
            "on_edit" => array(
                "position" => 2,
                "control" => "Select",
                "tip" => "Select the author of the post from the list."
            )
        ),
        "title" => array(
            "value" => "",
            //"regexp" => array("/^.{1,255}$/i" => "Please enter a valid title."),
            "on_grid" => array("position" => 3),
            "on_edit" => array(
                "position" => 3,
                "control" => "Input",
                "tip" => "Enter the title of the post."
            )
        ),
        "content" => array(
            "value" => "",
            "on_edit" => array(
                "position" => 4,
                "control" => "Html"
            )
        ),
        "can_comment" => array(
            "heading" => "Can Comment?",
            "value" => NULL,
            "type" => "boolean",
            //"data_type" => array("boolean" => "Please select yes or no."),
            //"has_default" => TRUE,
            "on_grid" => array("position" => 5),
            "on_edit" => array(
                "position" => 5,
                "control" => "Checkbox",
                "tip" => "Tick the box if visitors can comment on this post."
            )
        ),
        "type" => array(
            "heading" => "Page or Post?",
            "value" => "",
            "type" => "object:PostType",
            //"regexp" => array("/^post|page$/" => "Is this a page or a post?"),
            "on_grid" => array("position" => 2),
            "on_edit" => array(
                "position" => 6,
                "control" => "Select"
            )
        ),
    );
    
    protected $_relationships = array(
        "tags" => array(
            "column" => "tag",
            "table" => "post_tags",
            "foreign" => "post",
            "primary" => "id",
            "collection" => "TagObject",
            "on_edit" => array(
                "position" => 7,
                "control" => "Checkboxes",
                "tip" => "Select one or more tags."
            )
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
    protected $_uid = "id";
}
