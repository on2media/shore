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
            "on_grid" => array("position" => 1),
            "on_edit" => array(
                "position" => 2,
                "control" => "DateTimePicker",
                "tip" => "Use the date picker to select a date and time."
            )
        ),
        "approved" => array(
            "value" => NULL,
            "type" => "boolean",
            //"has_default" => TRUE,
            "on_grid" => array("position" => 4),
            "on_edit" => array(
                "position" => 7,
                "control" => "Checkbox",
                "tip" => "Only approved posts are shown."
            )
        ),
        "name" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array("test" => "/^.{1,255}$/i", "message" => "Please enter your name.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 3),
            "on_edit" => array(
                "position" => 3,
                "control" => "Input"
            )
        ),
        "email" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array(
                    "test" => "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i",
                    "message" => "Please enter a valid email address."
                )
            ),
            "required" => TRUE,
            "on_edit" => array(
                "position" => 4,
                "control" => "Input"
            )
        ),
        "website" => array(
            "value" => NULL,
            "validation" => array(
                "regexp" => array(
                    "test" => "/^(http:\/\/.{1,248}|)$/i",
                    "message" => "Please enter a valid website address starting http://."
                )
            ),
            "on_edit" => array(
                "position" => 5,
                "control" => "Input"
            )
        ),
        "content" => array(
            "value" => "",
            "type" => "text",
            "validation" => array(
                "regexp" => array("test" => "/^.+$/im", "message" => "Please enter a comment.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 4, "heading" => "Comment"),
            "on_edit" => array(
                "position" => 6,
                "control" => "Textarea",
                "tip" => "Edit the comment."
            )
        )
    );
    
    protected $_customColumns = array(
        "post_link" => array(
            "heading" => "Post",
            "position" => 2
        )
    );
    
    protected $_order = "received DESC";
    protected $_cite = "received";
    protected $_uid = "id";
    
    /**
     *
     */
    public function citePostLink()
    {
        return sprintf("<a href=\"%s/posts/edit/%d/\">%s</a>",
            _BASE . DIR_ADMIN,
            $this->getPost()->uid(),
            $this->citePost()
        );
    }
}
