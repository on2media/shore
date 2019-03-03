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

ini_set('session.save_path', dirname(__FILE__) . DS . 'cache' . DS . 'sessions');

// empty the session bin more often (1 in 10 chance)
ini_set("session.gc_probability", 1);
ini_set("session.gc_divisor", 10);

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
