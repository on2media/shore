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
define("MYSQL_DB_NAME"   ,  "blog");
define("MYSQL_PORT"      ,  "3306");

define("DIR_ADMIN"  ,  "cp");

define("AUTH_SESSION"   ,  "auth");
define("AUTH_MODEL"     ,  "AuthorObject");
define("AUTH_USERNAME"  ,  "email");
define("AUTH_PASSWORD"  ,  "password");
define("AUTH_SUPERUSER" ,  "super");

require_once("includes/app/Bootstrap.php");

// Homepage
Router::connect("/^$/", "Blog", "browse");

// Frontend Pages
Router::connect("/^(post|page)\/([0-9]*)\/$/i", "Post", "view");
Router::connect("/^author\/([0-9]*)\/$/i", "Author", "browse");
Router::connect("/^topic\/([0-9]*)\/$/i", "Topic", "browse");
Router::connect("/^tag\/([0-9]*)\/$/i", "Tag", "browse");

// Admin Pages
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/$/i", "Admin", "dashboard");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/logout\/$/i", "Admin", "logout");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/posts\/$/i", "Post", "grid");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/posts\/edit\/([0-9]*|new)\/$/i", "Post", "edit");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/comments\/$/i", "Comment", "grid");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/comments\/edit\/([0-9]*|new)\/$/i", "Comment", "edit");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/topics\/$/i", "Topic", "grid");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/topics\/edit\/([0-9]*|new)\/$/i", "Topic", "edit");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/tags\/$/i", "Tag", "grid");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/tags\/edit\/([0-9]*|new)\/$/i", "Tag", "edit");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/authors\/$/i", "Author", "grid");
Router::connect("/^" . preg_quote(DIR_ADMIN) . "\/authors\/edit\/([0-9]*|new)\/$/i", "Author", "edit");

// Default Page (404)
Router::connect("/^.*$/", "StaticPage", "notFound");

Router::route();
