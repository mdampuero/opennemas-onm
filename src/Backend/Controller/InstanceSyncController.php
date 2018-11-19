<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for the instance synchronization manager
 *
 * @package Backend_Controllers
 */
class InstanceSyncController extends Controller
{
    /**
     * Lists all the instances synced
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function listAction()
    {
        $syncParameters = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('sync_params');

        return $this->render('instance_sync/list.tpl', [
            'elements' => $syncParameters
        ]);
    }

    /**
     * Creates a new synchronized remote instance
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('instance_sync/new.tpl');
        }

        // Filter params
        $data = [
            'site_url'   => $request->request->filter('site_url', '', FILTER_VALIDATE_URL),
            'username'   => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
            'password'   => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'site_color' => $request->request->filter('site_color', '', FILTER_SANITIZE_STRING),
            'categories' => $request->request->get('categories'),
        ];

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        // Get saved settings if exists (update action)
        $siteData   = [];
        $syncParams = $ds->get('sync_params');

        if (!empty($syncParams)) {
            $siteData = array_merge($syncParams, [ $data['site_url'] => $data ]);
        } else {
            $siteData = [ $data['site_url'] => $data ];
        }

        try {
            $ds->set('sync_params', $siteData);

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Configuration saved successfully')
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while saving the configuration')
            );
        }

        return $this->redirect($this->generateUrl(
            'admin_instance_sync_show',
            [ 'site_url' => $data['site_url'] ]
        ));
    }

    /**
     * Fetches the categories from an URL
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function fetchCategoriesAction(Request $request)
    {
        $siteUrl  = $request->request->filter('site_url', '', FILTER_VALIDATE_URL);
        $username = $request->request->filter('username', '', FILTER_SANITIZE_STRING);
        $password = $request->request->filter('password', '', FILTER_SANITIZE_STRING);

        $element   = [];
        $authError = false;
        $result    = '';
        if (!empty($siteUrl)) {
            $siteUrl = rtrim(trim($siteUrl), '/\\');
            $url     = $siteUrl . '/ws/categories/lists.xml';

            // Fetch content using digest authentication
            $xmlString = $this->get('core.helper.http_fetcher')
                ->getContentFromUrlWithDigestAuth($url, $username, $password);

            // Load xml object
            $result = simplexml_load_string($xmlString);

            // Check for bad authentication
            if (isset($result->error)) {
                $authError = true;
            }

            // Fetch params from db
            $syncParams = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('sync_params');

            // Get site values if exists
            if ($syncParams) {
                foreach ($syncParams as $site => $values) {
                    if (preg_match('@' . $site . '@', $siteUrl)) {
                        $element = $values;
                    }
                }
            }
        }

        return $this->render('instance_sync/partials/_list_categories.tpl', [
            'site'           => $element,
            'all_categories' => $result,
            'has_auth_error' => $authError,
        ]);
    }

    /**
     * Displays the instance information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function showAction(Request $request)
    {
        $siteUrl = $request->query->filter('site_url', '', FILTER_VALIDATE_URL);

        // Fetch params from db
        $syncParams = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('sync_params');

        // Get site values
        $element = $syncParams[$siteUrl];

        // Set url to fetch categories
        $url = $siteUrl . '/ws/categories/lists.xml';
        // Fetch content using digest authentication
        $xmlString = $this->get('core.helper.http_fetcher')
            ->getContentFromUrlWithDigestAuth(
                $url,
                $element['username'],
                $element['password']
            );
        // Load xml object
        $categories = simplexml_load_string($xmlString);

        // Fetch categories output
        $output = $this->renderView('instance_sync/partials/_list_categories.tpl', [
            'site'           => $element,
            'all_categories' => $categories
        ]);

        // Render view
        return $this->render('instance_sync/new.tpl', [
            'site'   => $element,
            'output' => $output,
        ]);
    }

    /**
     * Deletes a synced instance from the configuration
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function deleteAction(Request $request)
    {
        $siteUrl = $request->query->filter('site_url', '');

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        // Fetch params from db
        $syncParams = $ds->get('sync_params');

        // Search the instance by site_url
        $index = false;
        foreach ($syncParams as $key => $value) {
            if ($value['site_url'] == $siteUrl || $key == $siteUrl) {
                $index = $key;
                break;
            }
        }

        if ($index !== false) {
            unset($syncParams[$siteUrl]);
        }

        try {
            $ds->set('sync_params', $syncParams);

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Site configuration deleted successfully')
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while deleting this configuration')
            );
        }

        return $this->redirect($this->generateUrl('admin_instance_sync'));
    }
}
