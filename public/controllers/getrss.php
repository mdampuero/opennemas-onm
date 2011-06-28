<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

if($_GET["name"]=="Portada") {
    $category_name = "";
} else {
	$category_name = String_Utils::normalize_name($_GET["name"]);
    $category_name = String_Utils::clearSpecialChars($category_name);
    $category_name = String_Utils::setSeparator($category_name);

	//$category_name = strtolower(String_Utils::remove_accents($_GET["name"]));
}

Application::forward301('/rss/'.$category_name);
