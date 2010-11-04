<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

if($_GET["name"]=="Portada") {
    $category_name = "";
} else {
	$string_util = new String_Utils();
    $category_name = $string_util->normalize_name($_GET["name"]);
    $category_name = $string_util->clearSpecialChars($category_name);
    $category_name = $string_util->setSeparator($category_name);
    
	//$category_name = strtolower($string_util->remove_accents($_GET["name"]));
}

Application::forward301('/rss/'.$category_name);
