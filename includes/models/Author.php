<?php
/**
 *
 */

/**
 *
 */
class AuthorObject extends MySqlObject
{
    protected $_table = "authors";
    
    protected $_fields = array(
        "id" => array(
            "value" =>  NULL
        ),
        "name" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array("test" => "/^.{1,255}$/i", "message" => "Please enter your name.")
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 1),
            "on_edit" => array(
                "position" => 1,
                "control" => "Input",
                "tip" => "Enter the author's name."
            )
        ),
        "email" => array(
            "heading" => "Email Address",
            "value" => "",
            "validation" => array(
                "regexp" => array(
                    "test" => "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i",
                    "message" => "Please enter a valid email address."
                ),
                "unique" => array(
                    "message" => "This email address is used on another account."
                )
            ),
            "required" => TRUE,
            "on_grid" => array("position" => 2),
            "on_edit" => array(
                "position" => 2,
                "control" => "Input",
                "tip" => "Enter the author's email address. This can be used to login."
            )
        ),
        "password" => array(
            "value" => "",
            "validation" => array(
                "regexp" => array("test" => "/^[0-9a-f]{32}$/i", "message" => "Please enter a password.")
            ),
            "on_edit" => array(
                "position" => 3,
                "control" => "Password",
                "tip" => "Enter a password so the author can login."
            )
        ),
        "super" => array(
            "heading" => "Super User",
            "value" => NULL,
            "type" => "boolean",
            "on_edit" => array(
                "position" => 4,
                "control" => "Checkbox",
                "tip" => "Is the author a super user?"
            )
        ),
    );
    
    protected $_relationships = array(
        "posts" => array(
            "type" => "1-m",
            "value" => NULL,
            "column" => "id",
            "table" => "posts",
            "foreign" => "author",
            "primary" => "id",
            "collection" => "PostObject"
        ),
        "access" => array(
            "type" => "m-m",
            "value" => NULL,
            "column" => "access",
            "table" => "author_access",
            "foreign" => "author",
            "primary" => "id",
            "collection" => "AccessObject",
            "on_edit" => array(
                "position" => 5,
                "control" => "Checkboxes",
                "tip" => "Define the author's level of admin access."
            )
        )
    );
    
    protected $_order = "name";
    protected $_cite = "name";
    protected $_uid = "id";
    
    public function canAccess($access, $compare="OR")
    {
        if ($this->getSuper()) return TRUE;
        
        if (!is_array($access)) $access = array($access);
        
        $matches = 0;
        
        foreach ($access as $accessLevel) {
            
            foreach ($this->getAccess() as $level) {
                if ($level->getId() == $accessLevel) $matches++;
            }
            
        }
        
        return (($compare == "OR" && $matches == count($access)) || ($compare == "AND" && $matches > 0));
    }
}
