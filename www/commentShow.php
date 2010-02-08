<?php
require('config.inc.php');
require_once('core/application.class.php');

require_once('core/string_utils.class.php');

if ($_REQUEST["sid"]) {
    if ($_REQUEST["pid"]) {
        Application::forward301('http://clasica.xornal.com/commentShow.php?sid='.$_REQUEST["sid"].'&pid='.$_REQUEST["pid"]);

    } else {
        Application::forward301('http://clasica.xornal.com/commentShow.php?sid='.$_REQUEST["sid"]);
    }

}
