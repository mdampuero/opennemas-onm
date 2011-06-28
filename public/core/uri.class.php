<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
* Uri Class that implements the Uri generation for every content type
*
* @category Onm
* @package Onm
* @subpackage Uri
* @copyright Copyright (c) 2005-2010 OpenHost S.L. http://www.openhost.es)
* @license http://framework.zend.com/license
* @version    $Id: uri.class.php 1 2011-02-16 11:59:19Z frandieguez $
* @since Class available since Release 1.5.0 BSD License
*/
class Uri {

    static public $url_configurations = array(
        'article'       =>  array( 'articulo/_CATEGORY_/_DATE_/_SLUG_/_ID_.html' ),
        'opinion'       =>  array( 'opinion/_CATEGORY_/_DATE_/_SLUG_/_ID_.html' ),
        'opinions'      =>  array( 'opinions/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
        'opinion_author_frontpage'   =>  array( 'opinion/autor/_ID_/_SLUG_' ),
        'section'       =>  array( 'seccion/_ID_' ),
        'video'         =>  array( 'video/_CATEGORY_/_DATE_/_SLUG_/_ID_.html' ),
        'album'         =>  array( 'galeria/_CATEGORY_/_DATE_/_SLUG_/_ID_.html' ),
        'poll'          =>  array( 'encuesta/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
        'static_page'   => array( 'static_page/_SLUG_.html' ),
        'ad'            =>  array( 'publicidad/_ID_.html' ),
        'articleNewsletter'=>  array( '_CATEGORY_/_DATE_/_ID_' ),
    );

    public function __construct($params = array())
    {
        Uri::getConfig();
    }


    static public function getConfig($params = array())
    {
        // $url_configurations should be in config.inc.php

        if (isset($GLOBALS['url_configurations'])) {
            $config = array_merge( Uri::$url_configurations, $GLOBALS['url_configurations'], $params );
        } else {
            $config = $GLOBALS['url_configurations'];
        }
        return $config;
    }

    static public function generate($content_type, $params = array())
    {
        $config  = Uri::getConfig();
        $config = $config[$content_type];


        if (!isset($content_type)) {
            $GLOBALS['application']->logger->debug('Error: Uri::generate should get $content_type and parameters');
            $GLOBALS['application']->errors[] = 'Error: Uri::generate should get $content_type and parameters';
            return;
        }

        $finaluri = '';
        $replacements = array();
        foreach ($params as $tokenKey => $tokenValue ) {
            $replacements["@_".strtoupper($tokenKey)."_@"] = $tokenValue;
        }

        $finaluri = preg_replace(array_keys($replacements), array_values($replacements), $config);

        return trim($finaluri[0]);
    }
}
