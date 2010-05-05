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


class Onm_Controller_Plugin_Locale extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		list($lang, $dirname) = $this->_getLang($request);
        
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);        
        
		$moFile = SITE_ADMIN_PATH . 'themes/default/locale/' . $dirname . '/messages.mo';
        $logger->log($moFile, Zend_Log::INFO);
        $logger->log(file_exists($moFile), Zend_Log::INFO);
		
        if(file_exists($moFile)) {
			$translate = new Zend_Translate('gettext', $moFile, $lang);
            
            $logger->log($translate, Zend_Log::INFO);
            
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
        
        if($lang === false) {
            // Get cookie
            $lang = $request->getCookie('lang', false);
        }
        
        if(($lang !== false) && (Zend_Locale::isLocale($lang))) {
            $locale = new Zend_Locale($lang);
        } else {
            $locale = new Zend_Locale();
        }
        
        $lang    = strtolower( $locale->getLanguage() );
        $dirname = strtolower( $locale->getTranslation($lang, 'language', 'en') );
        
        return array($lang, $dirname);
    }
}