<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

if ($_REQUEST["sid"]) {
    
    if ($_REQUEST["pid"]) {
        Application::forward301('http://clasica.xornal.com/commentShow.php?sid='.$_REQUEST["sid"].'&pid='.$_REQUEST["pid"]);
    } else {
        Application::forward301('http://clasica.xornal.com/commentShow.php?sid='.$_REQUEST["sid"]);
    }

}
