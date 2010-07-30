<?php
/**
 *
 */

/**
 *
 */
class AccessObject extends MapObject
{
    protected $_fields = array(
        "id" => array("value" =>  NULL),
        "rank" => array("value" => NULL),
        "super" => array("value" => NULL), // Access can only be granted by superusers.
        "description" => array("value" => NULL)
    );
    
    protected $_values = array(
        array("id" =>  4,  "rank" =>  1,  "super" => 0,  "description" => "View Posts"),
        array("id" =>  1,  "rank" =>  2,  "super" => 0,  "description" => "Add, Edit & Delete All Posts & Pages"),
        array("id" =>  2,  "rank" =>  3,  "super" => 0,  "description" => "Add Posts, Edit Authored Posts"),
        array("id" => 10,  "rank" =>  4,  "super" => 0,  "description" => "Approve Posts"),
        array("id" =>  3,  "rank" =>  5,  "super" => 0,  "description" => "View Comments"),
        array("id" =>  5,  "rank" =>  6,  "super" => 0,  "description" => "Edit (Approve) & Delete Comments"),
        array("id" =>  7,  "rank" =>  7,  "super" => 0,  "description" => "View, Add, Edit & Delete Topics"),
        array("id" =>  6,  "rank" =>  8,  "super" => 0,  "description" => "View, Add, Edit & Delete Tags"),
        array("id" =>  8,  "rank" =>  9,  "super" => 0,  "description" => "View, Add, Edit & Delete Authors"),
        array("id" =>  9,  "rank" => 10,  "super" => 0,  "description" => "View & Edit Their Own Details"),
        array("id" => 11,  "rank" => 11,  "super" => 1,  "description" => "Assign Super Users")
    );
    
    protected $_order = "rank";
    protected $_cite = "description";
    protected $_uid = "id";
}
