<?php
/**
 * Index Page
 *
 * We use the .htaccess so that all requests are passed through this file.
 *
 * @package core
 */

require_once("includes/app/Bootstrap.php");

Router::connect("/^$/", "Blog", "browse");
Router::connect("/^(post|page)\/([0-9]*)\/?$/", "Post", "view");
Router::connect("/^author\/([0-9]*)\/?$/", "Author", "browse");
Router::connect("/^tag\/([0-9]*)\/?$/", "Tag", "browse");
Router::connect("/^.*$/", "StaticPage", "notFound");

Router::route();
