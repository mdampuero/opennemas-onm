<?php
require('config.inc.php');
require_once('core/application.class.php');

require_once('core/string_utils.class.php');

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
