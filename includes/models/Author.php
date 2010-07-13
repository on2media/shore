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
            "data_type" => array("primary" => "Invalid author identifier.")
        ),
        "name" => array(
            "value" => "",
            "regexp" => array("/^.{1,255}$/i" => "Please enter your name.")
        ),
        "email" => array(
            "value" => "",
            "regexp" => array("/^.{1,50}$/i" => "Please enter a valid email address."),
            "data_type" => array("unique" => "This email address is used on another account.")
        ),
        "password" => array(
            "value" => "",
            "regexp" => array("/^.{1,16}$/i" => "Please enter a valid password.")
            // IS MD5'd
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
}
