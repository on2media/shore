<?php
/**
 * From here the site settings are defined before the execution is passed on to the relevant
 * controller class.
 *
 * @package core
 */

// create a shorthand version the DIRECTORY_SEPARATOR constant
if (!defined("DS")) define("DS", DIRECTORY_SEPARATOR);

// we haven't defined the site as live or in development, so assume it's live
if (!defined("IS_LIVE")) define("IS_LIVE", TRUE);

// define the default tag used to show errors
if (!defined("CONTROL_ERROR_TAG")) define("CONTROL_ERROR_TAG", "small");

// set the default timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// empty the session bin more often (1 in 10 chance)
ini_set("session.gc_probability", 1);
ini_set("session.gc_divisor", 10);

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

// automatically define some system variables - paths and urls
if (!defined("_PATH")) define("_PATH", realpath(dirname(__FILE__) . DS . ".." . DS . "..") . DS);

define("_PROTOCOL", (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] == "off" ? "http" : "https"));

$urlDir = dirname($_SERVER["SCRIPT_NAME"]);
$urlDir = (in_array($urlDir, array("\\", "/", ".")) ? "/" : "$urlDir/");
$base = "://" . $_SERVER["HTTP_HOST"] . str_replace("%2F", "/", rawurlencode($urlDir));

define("_BASE", _PROTOCOL . $base);
define("_BASE_HTTP", "http" . $base);
define("_BASE_HTTPS", "https" . $base);

if (!$urlParts = @parse_url($_SERVER["REQUEST_URI"])) exit(); $urlPath = $urlParts["path"];
if (!$page = substr(rawurldecode($urlParts["path"]), strlen($urlDir))) $page = "";
define("_PAGE", $page);

define("_QS", (isset($urlParts["query"]) ? "?" . $urlParts["query"] : ""));

if (strpos(_PAGE, ".") === FALSE && _PAGE != "" && substr(_PAGE, -1) != "/") {
    @header($_SERVER["SERVER_PROTOCOL"] . " 301 Permanent Redirect");
    @header("Location: " . _BASE . _PAGE . "/");
    exit();
}

// include Smarty
require_once(realpath(dirname(__FILE__) . DS . "vendor" . DS . "smarty") . DS . "Smarty.class.php");

// include Swift Mailer
require_once(realpath(dirname(__FILE__) . DS . "vendor" . DS . "swift") . DS . "swift_required.php");

// include FPDF
require_once(realpath(dirname(__FILE__) . DS . "vendor" . DS . "fpdf") . DS . "fpdf.php");

// include FPDF
require_once(realpath(dirname(__FILE__) . DS . "vendor" . DS . "lessphp") . DS . "lessc.inc.php");

// class autoloader
function classAutoloader($className)
{
    $inc = array();
    
    switch (TRUE) {
        
        case (preg_match("/^([A-Za-z0-9]+)Controller$/", $className, $matches)):
            $inc[] = _PATH . DIR_CONTROLLERS . DS . $matches[1] . ".php";
            $inc[] = dirname(__FILE__) . DS . "controllers" . DS . $matches[1] . ".php";
            break;
        
        case (preg_match("/^([A-Za-z0-9]+)Component$/", $className, $matches)):
            $inc[] = _PATH . DIR_COMPONENTS . DS . $matches[1] . ".php";
            $inc[] = dirname(__FILE__) . DS . "components" . DS . $matches[1] . ".php";
            break;
        
        case (preg_match("/^([A-Za-z0-9]+)Control$/", $className, $matches)):
            $inc[] = _PATH . DIR_CONTROLS . DS . $matches[1] . ".php";
            $inc[] = dirname(__FILE__) . DS . "controls" . DS . $matches[1] . ".php";
            break;
        
        case (
            !in_array($className, array("MySqlObject", "MapObject", "SessionObject")) &&
            preg_match("/^([A-Za-z0-9]+)Object$/", $className, $matches)
        ):
            $inc[] = _PATH . DIR_MODELS . DS . $matches[1] . ".php";
            $inc[] = dirname(__FILE__) . DS . "models" . DS . $matches[1] . ".php";
            break;
        
        default:
            if (defined("DIR_USERAPP")) $inc[] = _PATH . DIR_USERAPP . DS . $className . ".php";
            $inc[] = _PATH . DIR_APP . DS . $className . ".php";
            $inc[] = dirname(__FILE__) . DS . $className . ".php";
            break;
        
    }
    
    foreach ($inc as $filename) {
        if (file_exists($filename) && include_once($filename)) return;
    }
    
    throw new Exception("Class autoloading failed for '" . htmlspecialchars($className) . "'");
    exit();
}

spl_autoload_register('classAutoloader');

/**
 * Setup session timeout
 *
 * Each time the bootstrap is called the last activity time is updated. If it's been longer
 * than the session timeout (an hour by default) the session is respawned removing all session
 * data.
 */
$maxLifetime = (defined("SESSION_TIMEOUT") ? SESSION_TIMEOUT : 3600 ); // an hour by default
@ini_set("session.gc_maxlifetime", $maxLifetime); // bin session after the maximum lifetime.
$session = Session::getInstance();
if ($session->getLastActivity() && (time() - $session->getLastActivity()) > $maxLifetime) $session->respawn();
$session->setLastActivity(time());

/**
 * This function converts a variable name in the format foo_bar into the corresponding function
 * name FooBar.
 *
 * @param  string  $str  The variable name to convert
 * @return string
 */
function var2func($str)
{
    return preg_replace_callback("/_([a-z])/", "chrToUpper", ucfirst(strtolower($str)));
}

function chrToUpper($chr)
{
    return strtoupper($chr[1]);
}

/**
 * This function converts a function name in the format FooBar into the corresponding variable
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

/**
 *
 */
function var2label($var)
{
    return preg_replace("/([A-Z])/", " $1", var2func($var));
}
