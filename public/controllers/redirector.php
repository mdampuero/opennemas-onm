<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * THIS SCRIPT CHECK AVAILABILITY FOR A SID/PK_CONTENT
 * Check the sid param exists in database,
 *
 * http://.../redirector?sid=2009...00000
 * @see https://redmine.openhost.es/repositories/changes/xornal/trunk/patches/import_checker.php
 */

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

// Check param
if (!isset($_REQUEST['sid']) || !preg_match('/[0-9]{19}/', $_REQUEST['sid'])) {
    header('Location: ' . SITE_URL . 'index.php');
    exit(0);
}

list($code, $url) = Content::pkExists($_REQUEST['sid']);

/* $response = new stdClass();
$response->code = $code;
$response->url  = preg_replace('@([^:])[/]{2,}@', '\1/', SITE_URL . $url);

header('Content-type: application/json');
echo json_encode($response); */

echo $code . ' ' . preg_replace('@([^:])[/]{2,}@', '\1/', SITE_URL . $url);
