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
 * Start up and setup the app
*/
require_once('../bootstrap.php');

$width  = isset($_GET['width'])  ? $_GET['width']  : '176';
$height = isset($_GET['height']) ? $_GET['height'] :  '49';
$characters = isset($_GET['characters']) && $_GET['characters'] > 1 ? $_GET['characters'] : '5';

$captcha = new CaptchaSecurityImages($width, $height, $characters, dirname(__FILE__).'/media/fonts/monofont.ttf');
exit(0);
