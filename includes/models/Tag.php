<?php
/**
 *
 */

/**
 *
 */
class TagObject extends MySqlObject
{
    protected $_table = "tags";
    
    protected $_fields = array(
        "id" => array(
            "value" =>  NULL,
            "data_type" => array("primary" => "Invalid tag identifier.")
        ),
        "tag" => array(
            "value" => "",
            "regexp" => array("/^.{1,255}$/i" => "Please enter a valid tag.")
        )
    );
    
    protected $_relationships = array(
        "posts" => array(
            "column" => "post",
            "table" => "post_tags",
            "foreign" => "tag",
            "primary" => "id",
            "collection" => "PostObject"
        )
    );
    
    protected $_order = "tag";
}
