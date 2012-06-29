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
use Onm\Settings as s,
    Onm\Message  as m;
/**
 * Setup app
 */
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

// Check ACL
//Acl::checkOrForward('SYNC_ADMIN');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

/**
 * Check if module is configured, if not redirect to configuration form
*/
//TODO: implement

switch($action) {

    case 'config':

        if (isset($_POST['categories']) && !empty($_POST['categories'])) {
            // Filter params
            $syncParams = filter_input_array(INPUT_POST);
            $siteUrl = filter_input( INPUT_POST, 'site_url', FILTER_SANITIZE_URL);
            $siteColor = filter_input( INPUT_POST, 'site_color', FILTER_SANITIZE_STRING);

            $categoriesToSync = $syncParams['categories'];

            // Get saved settings if exists
            if ($syncSettings = s::get('sync_params')) {
                $syncParams = array_merge($syncSettings,array($siteUrl => $categoriesToSync));
            } else {
                $syncParams = array($siteUrl => $categoriesToSync);
            }

            // Get site colors
            if ($syncColorSettings = s::get('sync_colors')) {
                $syncColors = array_merge($syncColorSettings,array($siteUrl => $siteColor));
            } else {
                $syncColors = array($siteUrl => $siteColor);
            }

            if (s::set('sync_params', $syncParams) && s::set('sync_colors', $syncColors))
            {
                m::add(_('EFE module configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the EFE module configuration'), m::ERROR);
            }

            Application::forward(SITE_URL_ADMIN.'/controllers/web_services/client.php');

        } elseif (isset($_POST['submit'])) {
            $message = _('There is no configuration to save.');
            $tpl->assign('message', $message);
        }

        $tpl->display('web_services/config.tpl');

    break;

    case 'connect':

        $siteUrl = filter_input( INPUT_POST, 'site_url' , FILTER_VALIDATE_URL );

        if (isset($siteUrl) && !empty($siteUrl)) {
            $connectionUrl = $siteUrl.'/ws.php/categories/lists.xml';
            $xmlString = @file_get_contents($connectionUrl);
            if ($xmlString) {
                $categories = simplexml_load_string($xmlString);

                $availableCategories = array();
                foreach ($categories as $category) {
                    $availableCategories[] = $category;
                }
                $tpl->assign('categories', $availableCategories);
            }
        }

        $output = $tpl->fetch('web_services/partials/_list_categories.tpl');

        echo  $output ;

    break;

    case 'edit':

        $currentSiteUrl = filter_input( INPUT_GET, 'site_url' , FILTER_VALIDATE_URL );

        // Fetch all categories from site url
        $connectionUrl = $currentSiteUrl.'/ws.php/categories/lists.xml';
        $xmlString = file_get_contents($connectionUrl);
        $categories = simplexml_load_string($xmlString);

        $availableCategories = array();
        foreach ($categories as $category) {
            $availableCategories[] = $category;
        }

        // Fetch sync categories in config
        $syncParams = s::get('sync_params');
        $syncColors = s::get('sync_colors');
        $categoriesChecked = array();
        foreach ($syncParams as $siteUrl => $categories) {
            if (preg_match('@'.$currentSiteUrl.'@', $siteUrl)) {
                $categoriesChecked = $categories;
            }
        }

        if (array_key_exists($currentSiteUrl, $syncColors)) {
            $color = $syncColors[$currentSiteUrl];
        }

        // Show list
        $tpl->assign('site_url', $currentSiteUrl);
        $tpl->assign('site_color', $color);
        $tpl->assign('categories', $availableCategories);
        $tpl->assign('categories_checked', $categoriesChecked);
        $output = $tpl->fetch('web_services/partials/_list_categories.tpl');
        $tpl->assign('output', $output);
        $tpl->display('web_services/edit.tpl');

    break;

    case 'delete':

        $currentSiteUrl = filter_input( INPUT_GET, 'site_url' , FILTER_VALIDATE_URL );

        // Fetch sync categories in config
        $syncParams = s::get('sync_params');
        $syncColors = s::get('sync_colors');

        $categoriesChecked = array();
        foreach ($syncParams as $siteUrl => $categories) {
            if (preg_match('@'.$currentSiteUrl.'@', $siteUrl)) {
                $syncParamsToDelete = array($siteUrl => $categories);
            }
        }

        if (array_key_exists($currentSiteUrl, $syncColors)) {
            $syncColorToDelete = array($currentSiteUrl => $syncColors[$currentSiteUrl]);
        }

        $syncParams = array_diff_assoc($syncParams, $syncParamsToDelete);
        $syncColors = array_diff_assoc($syncColors, $syncColorToDelete);

        if (s::set('sync_params', $syncParams) && s::set('sync_colors', $syncColors))
        {
            m::add(_('Site configuration deleted successfully'), m::SUCCESS);
        } else {
            m::add(_('There was an error while deleting this configuration'), m::ERROR);
        }

        Application::forward(SITE_URL_ADMIN.'/controllers/web_services/client.php');

    break;

    default:

        if ($syncParams = s::get('sync_params')) {

            $syncColors = s::get('sync_colors');

            // Fetch all elements
            $allSites = array();
            $colors = array();
            foreach ($syncParams as $siteUrl => $categories) {
                $allSites[] = array ($siteUrl => $categories);
                if (array_key_exists($siteUrl, $syncColors)) {
                    $colors[$siteUrl] = $syncColors[$siteUrl];
                }
            }

            $tpl->assign('site_color', $colors);
            $tpl->assign('elements', $allSites);

        }

        $tpl->display('web_services/client.tpl');

    break;
}
