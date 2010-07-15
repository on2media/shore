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
            "value" =>  NULL,
            //"data_type" => array("primary" => "Invalid author identifier.")
        ),
        "name" => array(
            "value" => "",
            //"regexp" => array("/^.{1,255}$/i" => "Please enter your name."),
            "on_grid" => array("position" => 1, "heading" => "Name"),
            "on_edit" => array(
                "position" => 1,
                "control" => "Input",
                "tip" => "Enter the author's name."
            )
        ),
        "email" => array(
            "value" => "",
            //"regexp" => array("/^.{1,50}$/i" => "Please enter a valid email address."),
            //"data_type" => array("unique" => "This email address is used on another account."),
            "on_grid" => array("position" => 2, "heading" => "Email Address"),
            "on_edit" => array(
                "position" => 2,
                "control" => "Input",
                "tip" => "Enter the author's email address. This can be used to login."
            )
        ),
        "password" => array(
            "value" => "",
            //"regexp" => array("/^.{1,16}$/i" => "Please enter a valid password.")
            // IS MD5'd
            "on_edit" => array(
                "position" => 3,
                "control" => "Password",
                "tip" => "Enter a password so the author can login."
            )
        )
    );
    
    protected $_relationships = array(
        "posts" => array(
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
