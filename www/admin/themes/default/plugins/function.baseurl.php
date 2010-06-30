<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
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
 * @category   OpenNemas
 * @package    OpenNemas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Get baseurl using Zend_Controller_Front
 *
 * @uses Zend_Controller_Front
 * @param array $params
 * @param Smarty $smarty
 * @return string   Base url
 */
function smarty_function_baseurl($params, &$smarty)
{
    $fc = Zend_Controller_Front::getInstance();
    
    return $fc->getBaseUrl();
}