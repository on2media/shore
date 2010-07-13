<?php
/**
 * From here the site settings are defined before the execution is passed on to the relevant
 * controller class.
 *
 * @package core
 */

// create a shorthand version the DIRECTORY_SEPARATOR constant
define("DS", DIRECTORY_SEPARATOR);

// require the site configuration file
require_once(realpath(dirname(__FILE__) . DS . ".." . DS . ".." . DS . "config.php"));

// set the default timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// remove the effects of magic quotes if they're turned on
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    function stripMagicQuotes($var)
    {
        return (is_array($var) ? array_map('stripMagicQuotes', $var) : stripslashes($var));
    }

    $_GET     = stripMagicQuotes($_GET);
    $_POST    = stripMagicQuotes($_POST);
    $_COOKIE  = stripMagicQuotes($_COOKIE);
    $_REQUEST = stripMagicQuotes($_REQUEST);
}

// start a session
session_name(SESSION_NAME);
session_start();

// automatically define some system variables - paths and urls
define("_PATH", realpath(dirname(__FILE__) . DS . ".." . DS . "..") . DS);

$urlDir = dirname($_SERVER["SCRIPT_NAME"]);
$urlDir = (in_array($urlDir, array("\\", "/", ".")) ? "/" : "$urlDir/");
define("_BASE", "http://" . $_SERVER["HTTP_HOST"] . str_replace("%2F", "/", rawurlencode($urlDir)));

$urlParts = parse_url($_SERVER["REQUEST_URI"]);
$urlPath = $urlParts["path"];
define("_PAGE", substr(rawurldecode($urlParts["path"]), strlen($urlDir)));

define("_QS", (isset($urlParts["query"]) ? "?" . $urlParts["query"] : ""));

// include Smarty
require_once(realpath(dirname(__FILE__) . DS . "vendor" . DS . "smarty") . DS . "Smarty.class.php");

// class autoloader
function classAutoloader($className)
{
    switch (TRUE) {
        
        case (preg_match("/^([A-Za-z]+)Controller$/", $className, $matches)):
            $inc = _PATH . "includes" . DS . "controllers" . DS . $matches[1] . ".php";
            break;
        
        case ($className != "MySqlObject" && preg_match("/^([A-Za-z]+)Object$/", $className, $matches)):
            $inc = _PATH . "includes" . DS . "models" . DS . $matches[1] . ".php";
            break;
        
        default:
            $inc = _PATH . "includes" . DS . "app" . DS . $className . ".php";
            break;
        
    }
    
    if (!include_once($inc)) {
        trigger_error("Class autoloading failed for '" . htmlentities($className) . "'", E_USER_ERROR);
        exit();
    }
}

spl_autoload_register('classAutoloader');

/**
 * This function converts a variable name in the format foo_bar into the corresponding function
 * name fooBar.
 *
 * @param  string  $str  The variable name to convert
 * @return string
 */
function var2func($str)
{
    $chrToUpper = create_function("\$chr", "return strtoupper(\$chr[1]);");
    return preg_replace_callback("/_([a-z])/", $chrToUpper, ucfirst(strtolower($str)));
}

/**
 * This function converts a function name in the format fooBar into the corresponding variable
 * name foo_bar.
 *
 * @param  string  $str  The function name to convert
 * @return string
 */
function func2var($str)
{
    $str[0] = strtolower($str[0]);
    return strtolower(preg_replace("/([A-Z])/", "_$1", $str));
}
