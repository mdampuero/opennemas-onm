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
 * @package Onm
 * @subpackage Utilities
 * @author Fran Dieguez <fran@openhost.es>
 */
class Uri
{

    /**
     * List of all default uri settings
     *
     * @var array
     **/
    private static $urlConfigurations = array(
       'article'   =>  array( 'articulo/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'opinion'   =>  array( 'opinion/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'blog'      =>  array( 'blog/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'opinion_author_frontpage'  =>  array( 'opinion/autor/_ID_/_SLUG_'),
       'blog_author_frontpage'   =>  array( 'blog/author/_SLUG_'),
       'section'   =>  array( 'seccion/_ID_'),
       'video'     =>  array( 'video/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'album'     =>  array( 'album/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'poll'      =>  array( 'encuesta/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'static_page'=> array( 'estaticas/_SLUG_.html'),
       'ad'        =>  array( 'publicidad/_ID_.html'),
       'articleNewsletter' => array( 'seccion/_CATEGORY_/#_ID_'),
       'kiosko'  =>  array( 'portadas-papel/_CATEGORY_/_DATE__ID_.html'),
       'letter'  =>  array( 'cartas-al-director/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'special' =>  array( 'especiales/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
       'book'    =>  array( 'libro/_CATEGORY_/_SLUG_/_DATE__ID_.html'),
    );

    /**
     * Initializes the Uri object.
     *
     **/
    public function __construct()
    {
        Uri::getConfig();
    }

    /**
     * Returns the list of configurations for uri generation
     *
     * @param array $params parameters for modify function behaviour.
     *
     * @return array the array of configurations
     **/
    public static function getConfig($params = array())
    {
        if (isset($GLOBALS['url_configurations'])) {
            $config = array_merge(
                self::$urlConfigurations,
                $GLOBALS['url_configurations'],
                $params
            );
        } else {
            $config = self::$urlConfigurations;
        }

        return $config;
    }

    /**
     * Returns a generated uri for a content type given some params
     *
     * @param string $contentType the content type to generate the url
     * @param array  $params the list of params required to generate the url
     *
     * @return string the uri generated
     **/
    public static function generate($contentType, $params = array())
    {

        $config = Uri::getConfig();
        if (array_key_exists($contentType, $config)) {
            $config = $config[$contentType];
        } else {
            return '';
        }

        if (!isset($contentType)) {
            $GLOBALS['application']->logger->debug('Error: Uri::generate should get $contentType and parameters');
            $GLOBALS['application']->errors[] = 'Error: Uri::generate should get $contentType and parameters';

            return;
        }

        $finaluri = '';
        $replacements = array();
        foreach ($params as $tokenKey => $tokenValue) {
            $replacements["@_" . strtoupper($tokenKey) . "_@"] = $tokenValue;
        }

        $finaluri = preg_replace(
            array_keys($replacements),
            array_values($replacements),
            $config
        );

        return trim($finaluri[0]);
    }
}
