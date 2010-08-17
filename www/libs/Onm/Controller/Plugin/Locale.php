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
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Onm_Controller_Plugin_Locale
 * 
 * @package    Onm
 * @subpackage Controller
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Locale.php 1 2010-08-16 12:30:36Z vifito $
 */
class Onm_Controller_Plugin_Locale extends Zend_Controller_Plugin_Abstract
{
    
    /**
     * preDispatch
     * 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $translate = null;
        
        // Core translation (default module translations) {{{
        list($locale, $dirname) = $this->_getLang($request);
        $moFile = APPLICATION_PATH . '/themes/default/locale/' . $dirname .
            '/messages.mo';
        
        if (file_exists($moFile)) {
            $translate = new Zend_Translate('gettext', $moFile, $locale);
        }
        // }}}
        
        // Load language file for module {{{
        $moduleName = $request->getModuleName();
        if ($moduleName != 'default') {
            $moFile = APPLICATION_PATH . '/modules/' . $moduleName .
                '/languages/' . $dirname . '/messages.mo';
            
            if (file_exists($moFile)) {
                if ($translate != null) {
                    $translate->addTranslation($moFile, $locale);
                } else {
                    $translate = new Zend_Translate(
                        'gettext', $moFile, $locale
                    );
                }
                
            }
        }        
        // }}}
        
        // Set in registry
        if ($translate != null) {
            Zend_Registry::set('Zend_Translate', $translate);
        }
    }

    /**
     * Get language name (ex.- galician, spanish, ...)
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return string
     */
    private function _getLang($request)
    {
        // Get :lang from querystring        
        $lang = $request->getParam('lang', false);
        
        if ($lang === false) {
            // Get cookie
            $lang = $request->getCookie('lang', false);
        }
        
        if (($lang !== false) && (Zend_Locale::isLocale($lang))) {
            $locale = new Zend_Locale($lang);
        } else {
            $locale = new Zend_Locale();
        }
        
        $lang    = strtolower($locale->getLanguage());
        $dirname = strtolower($locale->getTranslation($lang, 'language', 'en'));
        
        return array($locale, $dirname);
    }
    
}