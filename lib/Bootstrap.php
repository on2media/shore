<?php
/**
 * From here the site settings are defined before the execution is passed on to the relevant
 * controller class.
 *
 * @package core
 */

require_once('utilities.php');

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
define('URL_DIR', str_replace("%2F", "/", rawurlencode($urlDir)));

$base = "://" . $_SERVER["HTTP_HOST"] . URL_DIR;

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

// include Emogrifier
require_once(realpath(dirname(__FILE__) . DS . "vendor" . DS . "pelago") . DS . "emogrifier.php");

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
            !in_array($className, array("MySqlObject", "MySqlViewObject", "MapObject", "SessionObject")) &&
            preg_match("/^([A-Za-z0-9]+)Object$/", $className, $matches)
        ):
            $inc[] = _PATH . (defined("DIR_OBJECTS") ? DIR_OBJECTS : DIR_MODELS) . DS . $matches[1] . ".php";
            if (defined("DIR_LIBRARIES")) $inc[] = _PATH . DIR_LIBRARIES . DS . $matches[1] . ".php";

            $inc[] = dirname(__FILE__) . DS . (defined("DIR_OBJECTS") ? "objects" : "models") . DS . $matches[1] . ".php";
            break;
        
        case (
            defined("DIR_OBJECTS") &&
            preg_match("/^([A-Za-z0-9]+)Model$/", $className, $matches)
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
    
    //throw new Exception("Class autoloading failed for '" . htmlspecialchars($className) . "'");
    //exit();
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
