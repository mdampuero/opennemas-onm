<?php
/**
 * Handles the actions for the instance synchronization manager
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the instance synchronization manager
 *
 * @package Backend_Controllers
 **/
class InstanceSyncController extends Controller
{
    /**
     * Lists all the instances synced
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     *
     * @CheckModuleAccess(module="SYNC_MANAGER")
     **/
    public function listAction()
    {
        $allSites = $colors = array();

        if ($syncParams = s::get('sync_params')) {
            $syncColors = s::get('sync_colors');

            // Fetch all elements
            foreach ($syncParams as $siteUrl => $categories) {
                $allSites[] = array ($siteUrl => $categories);
                if (array_key_exists($siteUrl, $syncColors)) {
                    $colors[$siteUrl] = $syncColors[$siteUrl];
                }
            }
        }

        return $this->render(
            'instance_sync/list.tpl',
            array(
                'site_color' => $colors,
                'elements'   => $allSites
            )
        );
    }

    /**
     * Creates a new synchronized remote instance
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     *
     * @CheckModuleAccess(module="SYNC_MANAGER")
     **/
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('instance_sync/new.tpl');
        }

        // Filter params
        $syncParams = filter_input_array(INPUT_POST);
        $siteUrl    = $request->request->filter('site_url', '', FILTER_SANITIZE_URL);
        $siteColor  = $request->request->filter('site_color', '', FILTER_SANITIZE_STRING);

        $categoriesToSync = $syncParams['categories'];

        // Get saved settings if exists
        if ($syncSettings = s::get('sync_params')) {
            $syncParams = array_merge($syncSettings, array($siteUrl => $categoriesToSync));
        } else {
            $syncParams = array($siteUrl => $categoriesToSync);
        }

        // Get site colors
        if ($syncColorSettings = s::get('sync_colors')) {
            $syncColors = array_merge($syncColorSettings, array($siteUrl => $siteColor));
        } else {
            $syncColors = array($siteUrl => $siteColor);
        }

        if (s::set('sync_params', $syncParams)
            && s::set('sync_colors', $syncColors)
        ) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Configuration saved successfully')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while saving the configuration')
            );
        }

        return $this->redirect($this->generateUrl('admin_instance_sync_show', ['site_url' => $siteUrl]));
    }

    /**
     * Fetches the categories from an URL
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     *
     * @CheckModuleAccess(module="SYNC_MANAGER")
     **/
    public function fetchCategoriesAction(Request $request)
    {
        $siteUrl = $request->request->filter('site_url', '', FILTER_VALIDATE_URL);

        $categoriesChecked = $availableCategories = array();
        if (isset($siteUrl) && !empty($siteUrl)) {
            $connectionUrl = $siteUrl.'/ws/categories/lists.xml';
            $xmlString = @file_get_contents($connectionUrl);
            if ($xmlString) {
                $categories = simplexml_load_string($xmlString);

                $availableCategories = array();
                foreach ($categories as $category) {

                    if (!empty($category->submenu)) {
                        foreach ($category->submenu as $subcategory) {

                            $category->items[] = $subcategory;
                        }
                    }
                    $availableCategories[] = $category;
                }
            }

            // Fetch sync categories in config
            $syncParams = s::get('sync_params', array());
            $categoriesChecked = array();
            if ($syncParams) {
                foreach ($syncParams as $siteUrl => $categories) {
                    if (preg_match('@'.$siteUrl.'@', $siteUrl)) {
                        $categoriesChecked = $categories;
                    }
                }
            }
        }

        return $this->render(
            'instance_sync/partials/_list_categories.tpl',
            array(
                'categories_checked' => $categoriesChecked,
                'categories'         => $availableCategories,
                'loading'            => true,
            )
        );
    }

    /**
     * Displays the instance information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     *
     * @CheckModuleAccess(module="SYNC_MANAGER")
     **/
    public function showAction(Request $request)
    {
        $siteUrl = $request->query->filter('site_url', '', FILTER_VALIDATE_URL);

        // Fetch all categories from site url
        $connectionUrl = $siteUrl.'/ws/categories/lists.xml';
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
        foreach ($syncParams as $site => $categories) {
            if (preg_match('@'.$site.'@', $siteUrl)) {
                $categoriesChecked = $categories;
            }
        }

        if (array_key_exists($siteUrl, $syncColors)) {
            $color = $syncColors[$siteUrl];
        }

        $output = $this->renderView(
            'instance_sync/partials/_list_categories.tpl',
            array(
                'site_url'           => $siteUrl,
                'site_color'         => $color,
                'categories'         => $availableCategories,
                'categories_checked' => $categoriesChecked,
            )
        );

        // Show list
        return $this->render(
            'instance_sync/new.tpl',
            array(
                'site_url'           => $siteUrl,
                'site_color'         => $color,
                'categories'         => $availableCategories,
                'categories_checked' => $categoriesChecked,
                'output'             => $output,
            )
        );
    }

    /**
     * Deletes a synced instance from the configuration
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('INSTANCE_SYNC_ADMIN')")
     *
     * @CheckModuleAccess(module="SYNC_MANAGER")
     **/
    public function deleteAction(Request $request)
    {
        $siteUrl = $request->query->filter('site_url', '', FILTER_VALIDATE_URL);

        // Fetch sync categories in config
        $syncParams = s::get('sync_params');
        $syncColors = s::get('sync_colors');

        if (array_key_exists($siteUrl, $syncParams)) {
            unset($syncParams[$siteUrl]);
        }

        if (array_key_exists($siteUrl, $syncColors)) {
            unset($syncColors[$siteUrl]);
        }

        if (s::set('sync_params', $syncParams)
            && s::set('sync_colors', $syncColors)
        ) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Site configuration deleted successfully')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while deleting this configuration')
            );
        }

        return $this->redirect($this->generateUrl('admin_instance_sync'));
    }
}
