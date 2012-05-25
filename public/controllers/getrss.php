<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

if ($_GET["name"]=="Portada") {
    $category_name = "";
} else {
    $category_name = StringUtils::normalize_name($_GET["name"]);
    $category_name = StringUtils::clearSpecialChars($category_name);
    $category_name = StringUtils::setSeparator($category_name);

    //$category_name = strtolower(StringUtils::remove_accents($_GET["name"]));
}

Application::forward301('/rss/'.$category_name);
