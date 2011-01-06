<?php
/**
 * Index Page
 *
 * We use the .htaccess so that all requests are passed through this file. This is also where the
 * settings are stored for each site.
 *
 * @package core
 */

define("SESSION_NAME"       ,  "sid");
define("DEFAULT_TIMEZONE"   ,  "Europe/London");

define("MYSQL_HOST"      ,  "localhost");
define("MYSQL_USERNAME"  ,  "root");
define("MYSQL_PASSWORD"  ,  "");
define("MYSQL_DB_NAME"   ,  "db_name");
define("MYSQL_PORT"      ,  "3306");

define("AUTH_MODEL"  ,  "");

define("DIR_APP"          ,  "app");
define("DIR_COMPONENTS"   ,  DIR_APP . DIRECTORY_SEPARATOR . "components");
define("DIR_CONTROLLERS"  ,  DIR_APP . DIRECTORY_SEPARATOR . "controllers");
define("DIR_CONTROLS"     ,  DIR_APP . DIRECTORY_SEPARATOR . "controls");
define("DIR_MODELS"       ,  DIR_APP . DIRECTORY_SEPARATOR . "models");
define("DIR_VIEWS"        ,  DIR_APP . DIRECTORY_SEPARATOR . "views");

require_once("core/lib/Bootstrap.php");

// Default Page (404)
Router::connect("/^.*$/", "StaticPage", "notFound");

Router::route();
