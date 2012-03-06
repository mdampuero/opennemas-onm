<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

// Fetching HTTP vars
$action   = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING,  array('options' => array( 'default' => 'list')));

switch ($action) {
    case 'suggest-to-frontpage':
        //
        break;
}