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

class Uri
{

    /**
     * Uri settings
     **/
    private $_urlConfigurations = array(
       'article'   =>  array( 'articulo/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
       'opinion'   =>  array( 'opinion/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
       'opinion_author_frontpage'   =>  array( 'opinion/autor/_ID_/_SLUG_'),
       'section'   =>  array( 'seccion/_ID_'),
       'video'     =>  array( 'video/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
       'album'     =>  array( 'galeria/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
       'poll'      =>  array( 'encuesta/_CATEGORY_/_DATE_/_SLUG_/_ID_.html'),
       'static_page'=> array( 'static_page/_SLUG_.html'),
       'ad'        =>  array( 'publicidad/_ID_.html'),
    );

    /**
     * Initializes the Uri object.
     *
     * @param array $params parameters for modify function behaviour.
     **/
    public function __construct($params = array())
    {
        Uri::getConfig();
    }

    static public function getConfig($params = array())
    {
        if (isset($GLOBALS['url_configurations'])) {
            $config = array_merge(
                Uri::$_urlConfigurations,
                $GLOBALS['url_configurations'],
                $params
            );
        } else {
            $config = $GLOBALS['url_configurations'];
        }
        return $config;
    }

    static public function generate($contentType, $params = array())
    {

        $config = Uri::getConfig();
        $config = $config[$contentType];

        if (!isset($contentType)) {
            $GLOBALS['application']->logger->debug(
                _('Error: Uri::generate should get $contentType and parameters')
            );
            $GLOBALS['application']->errors[] =
                _(
                    'Error: Uri::generate should get '
                    .'$contentType and parameters'
                );

            return;
        }

        $finaluri = '';
        $replacements = array();
        foreach ($params as $tokenKey => $tokenValue) {
            $replacements["@_" . strtoupper($tokenKey) . "_@"] = $tokenValue;
        }

        $finaluri = preg_replace(
            array_keys($replacements), array_values($replacements), $config
        );
        return trim($finaluri[0]);
    }
}
