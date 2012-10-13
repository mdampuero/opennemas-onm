<?php
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
use Onm\Settings as s;
use Onm\Message  as m;

/**
 * Setup app
 */
require_once '../bootstrap.php';

require_once SITE_VENDOR_PATH.'/Restler/restler.php';
require_once SITE_VENDOR_PATH.'/Restler/xmlformat.php';

$r = new Restler();
$r->setSupportedFormats('JsonFormat', 'XmlFormat');
$r->addAPIClass('Instances');
$r->addAPIClass('Ads');
$r->addAPIClass('Contents');
$r->addAPIClass('Articles');
$r->addAPIClass('Opinions');
$r->addAPIClass('Comments');
$r->addAPIClass('Images');
$r->addAPIClass('Videos');
$r->addAPIClass('Categories');
$r->addAPIClass('Authors');

$r->handle();

