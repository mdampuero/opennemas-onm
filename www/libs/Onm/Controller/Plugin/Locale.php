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
		$lang = $this->_getLang($request);
		
		//$writer = new Zend_Log_Writer_Firebug();
		//$logger = new Zend_Log($writer);
		//$logger->log($lang, Zend_Log::INFO);
		
		$moFile = SITE_ADMIN_PATH . 'themes/default/locale/' . $lang . '/LC_MESSAGES/messages.mo';
		if(file_exists($moFile)) {
			$translate = new Zend_Translate('gettext', $moFile, $lang);
			Zend_Registry::set('translate', $translate);			
		}
		
		//$logger->log($translate, Zend_Log::INFO);
        
		// Gettext
		$language = $lang . '.UTF-8';
        putenv('LANG=' . $language);
        setlocale(LC_ALL, $language);
        
        $domain = 'messages';
        bind_textdomain_codeset($domain, 'UTF-8');
		
		// FIXME: fix path to locale 
        bindtextdomain($domain, SITE_ADMIN_PATH . 'themes/default/locale');
        textdomain($domain); 
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
        
        $lang = strtolower( $locale->getLanguage() );
		
		// FIXME
		switch($lang) {
			case 'gl':
				$lang = 'gl_ES';
			break;
			
			case 'es':
				$lang = 'es_ES';
			break;
			
			case 'en':
				$lang = 'en_GB';
			break;
		}
        
        return $lang;
    }
}