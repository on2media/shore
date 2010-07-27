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
            "on_grid" => array("position" => 1, "heading" => "Name"),
            "on_edit" => array(
                "position" => 1,
                "control" => "Input",
                "tip" => "Enter the author's name."
            )
        ),
        "email" => array(
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
            "on_grid" => array("position" => 2, "heading" => "Email Address"),
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
        )
    );
    
    protected $_relationships = array(
        "posts" => array(
            "type" => "1-m",
            "column" => "id",
            "table" => "posts",
            "foreign" => "author",
            "primary" => "id",
            "collection" => "PostObject"
        )
    );
    
    protected $_order = "name";
    protected $_cite = "name";
    protected $_uid = "id";
}
