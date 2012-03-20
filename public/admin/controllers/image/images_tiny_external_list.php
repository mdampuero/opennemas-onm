<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'/session_bootstrap.php');

/**
 * Setup Database Access
*/
$cm = new ContentManager();

$output = ''; // Here we buffer the JavaScript code we want to send to the browser.
$delimiter = "\n"; // for eye candy... code gets new lines
$category = $_GET['category'];
$numItems=30;

$output .= 'var tinyMCEImageList = new Array(';
list($photos, $pager)= $cm->find_pages('Photo', 'fk_content_type=8 and photos.media_type="image"', 'ORDER BY  created DESC ',1, $numItems, $category);

foreach($photos as $photo)
{
    $file='../media/images'.$photo->path_file.$photo->name;
    if(file_exists($file)){
        // We got ourselves a file! Make an array entry:
        $output .= $delimiter
            . '["'
            . utf8_encode($photo->title)
            . '", "'
            . utf8_encode("$file")
            . '"],';
    }
}

$output = substr($output, 0, -1); // remove last comma from array item list (breaks some browsers)
$output .= $delimiter;

$output .= ');'; // Finish code: end of array definition. Now we have the JavaScript code ready!

 header('Content-type: text/javascript'); // Make output a real JavaScript file!

echo $output;