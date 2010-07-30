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
            "value" =>  NULL
        ),
        "posted" => array(
            "value" => NULL,
            "type" => "timestamp:jS F Y \\a\\t g:ia",
            "validation" => array(
                "timestamp" => array("message" => "Please enter a valid time")
            ),
            "required" => TRUE,
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
            "validation" => array(
                "object" => array("object" => "Author", "message" => "Please select an author from the list.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 4),
            "on_edit" => array(
                "position" => 2,
                "control" => "Select",
                "tip" => "Select the author of the post from the list."
            )
        ),
        "title" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array("test" => "/^.{1,255}$/i", "message" => "Please enter a valid title.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 3),
            "on_edit" => array(
                "position" => 3,
                "control" => "Input",
                "tip" => "Enter the title of the post."
            )
        ),
        "topic" => array(
            "value" => NULL,
            "type" => "object:Topic",
            "validation" => array(
                "object" => array("object" => "Topic", "message" => "Please select a topic from the list.")
            ),
            "on_edit" => array(
                "position" => 8,
                "control" => "Select",
                "tip" => "You optionally may define the topic of this post."
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
            "on_grid" => array("position" => 6),
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
            "validation" => array(
                "object" => array("object" => "PostType", "message" => "Please select a type from the list.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 2),
            "on_edit" => array(
                "position" => 6,
                "control" => "Select"
            )
        ),
        "approved" => array(
            "heading" => "Approved?",
            "value" => NULL,
            "type" => "boolean",
            "on_grid" => array("position" => 5),
            "on_edit" => array(
                "position" => 7,
                "control" => "Checkbox",
                "tip" => "Only approved posts are shown."
            )
        )
    );
    
    protected $_relationships = array(
        "tags" => array(
            "type" => "m-m",
            "value" => NULL,
            "column" => "tag",
            "table" => "post_tags",
            "foreign" => "post",
            "primary" => "id",
            "collection" => "TagObject",
            "on_edit" => array(
                "position" => 9,
                "control" => "Checkboxes",
                "tip" => "Select one or more tags."
            )
        ),
        "comments" => array(
            "type" => "1-m",
            "value" => NULL,
            "column" => "id",
            "table" => "comments",
            "foreign" => "post",
            "primary" => "id",
            "collection" => "CommentObject"
        )
    );
    
    protected $_customColumns = array(
        "comment_count" => array(
            "heading" => "Comments",
            "position" => 7
        )
    );
    
    protected $_order = "posted DESC";
    protected $_cite = "title";
    protected $_uid = "id";
    
    /**
     *
     */
    public function citeCommentCount()
    {
        if (!$this->getCanComment()) return "- -";
        
        $approved = 0;
        $total = $this->getComments()->count();
        
        foreach ($this->getComments() as $comment) {
            $approved += ($comment->getApproved() ? 0 : 1);
        }
        
        return ($total - $approved) . ($approved > 0 ? " (<abbr title=\"You have comments awaiting approval.\">" . $approved . "</abbr>)" : "");
    }
}
