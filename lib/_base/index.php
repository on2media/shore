<?php
/**
 * Index Page
 *
 * We use the .htaccess so that all requests are passed through this file. This is alos where the
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

define("ADMIN_URL"  ,  "admin");

define("AUTH_SESSION"    ,  "auth");
define("AUTH_MODEL"      ,  "UserObject");
define("AUTH_USERNAME"   ,  "username");
define("AUTH_PASSWORD"   ,  "password");
define("AUTH_SUPERUSER"  ,  "superuser");

define("DIR_APP"          ,  "app");
define("DIR_COMPONENTS"   ,  "app" . DIRECTORY_SEPARATOR . "components");
define("DIR_CONTROLLERS"  ,  "app" . DIRECTORY_SEPARATOR . "controllers");
define("DIR_CONTROLS"     ,  "app" . DIRECTORY_SEPARATOR . "controls");
define("DIR_MODELS"       ,  "app" . DIRECTORY_SEPARATOR . "models");
define("DIR_VIEWS"        ,  "app" . DIRECTORY_SEPARATOR . "views");

require_once("core/lib/Bootstrap.php");

// Homepage
Router::connect("/^$/", "Controller", "function");

// Frontend Pages
Router::connect("/^(post|page)\/([0-9]*)\/$/i", "Post", "view");
Router::connect("/^tag\/([0-9]*)\/$/i", "Tag", "browse");

// Admin Pages
Router::connect("/^" . preg_quote(ADMIN_URL) . "\/$/i", "Admin", "dashboard");
Router::connect("/^" . preg_quote(ADMIN_URL) . "\/logout\/$/i", "Admin", "logout");
Router::connect("/^" . preg_quote(ADMIN_URL) . "\/posts\/$/i", "Post", "grid");
Router::connect("/^" . preg_quote(ADMIN_URL) . "\/posts\/edit\/([0-9]*|new)\/$/i", "Post", "edit");


// Default Page (404)
Router::connect("/^.*$/", "StaticPage", "notFound");

Router::route();
