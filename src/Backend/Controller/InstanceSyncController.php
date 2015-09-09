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
        $allSites = s::get('sync_params');

        return $this->render(
            'instance_sync/list.tpl',
            [
                'elements' => $allSites
            ]
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
        $data = [
            'site_url'   => $request->request->filter('site_url', '', FILTER_VALIDATE_URL),
            'username'   => $request->request->filter('username', '', FILTER_SANITIZE_STRING),
            'password'   => $request->request->filter('password', '', FILTER_SANITIZE_STRING),
            'site_color' => $request->request->filter('site_color', '', FILTER_SANITIZE_STRING),
            'categories' => $request->request->get('categories'),
        ];

        // Get saved settings if exists (update action)
        $siteData = [];
        if ($syncParams = s::get('sync_params')) {
            $siteData = array_merge($syncParams, [$data['site_url'] => $data]);
        } else {
            $siteData = [$data['site_url'] => $data];
        }

        if (s::set('sync_params', $siteData)) {
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

        return $this->redirect(
            $this->generateUrl(
                'admin_instance_sync_show',
                ['site_url' => $data['site_url']]
            )
        );
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
        $siteUrl  = $request->request->filter('site_url', '', FILTER_VALIDATE_URL);
        $username = $request->request->filter('username', '', FILTER_SANITIZE_STRING);
        $password = $request->request->filter('password', '', FILTER_SANITIZE_STRING);

        $element = [];
        $authError = false;
        if (!empty($siteUrl)) {
            $url = $siteUrl.'/ws/categories/lists.xml';
            // Fetch content using digest authentication
            $xmlString = $this->getContentFromUrlWithDigestAuth($url, $username, $password);

            // Load xml object
            $result = simplexml_load_string($xmlString);

            // Check for bad authentication
            if (isset($result->error)) {
                $authError = true;
            }
            // Fetch params from db
            $syncParams = s::get('sync_params');
            // Get site values if exists
            if ($syncParams) {
                foreach ($syncParams as $site => $values) {
                    if (preg_match('@'.$site.'@', $siteUrl)) {
                        $element = $values;
                    }
                }
            }
        }

        return $this->render(
            'instance_sync/partials/_list_categories.tpl',
            [
                'site'           => $element,
                'all_categories' => $result,
                'has_auth_error' => $authError,
            ]
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

        // Fetch params from db
        $syncParams = s::get('sync_params');

        // Get site values
        $element = $syncParams[$siteUrl];

        // Set url to fetch categories
        $url = $siteUrl.'/ws/categories/lists.xml';
        // Fetch content using digest authentication
        $xmlString = $this->getContentFromUrlWithDigestAuth(
            $url,
            $element['username'],
            $element['password']
        );
        // Load xml object
        $categories = simplexml_load_string($xmlString);

        // Fetch categories output
        $output = $this->renderView(
            'instance_sync/partials/_list_categories.tpl',
            [
                'site'           => $element,
                'all_categories' => $categories
            ]
        );

        // Render view
        return $this->render(
            'instance_sync/new.tpl',
            [
                'site'   => $element,
                'output' => $output,
            ]
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

        // Fetch params from db
        $syncParams = s::get('sync_params');

        if (array_key_exists($siteUrl, $syncParams)) {
            unset($syncParams[$siteUrl]);
        }

        if (s::set('sync_params', $syncParams)) {
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

    /**
     * Get content from a given url using http digest auth and curl
     *
     * @param $url the http server url
     *
     * @return $content the content from this url
     *
     **/
    private function getContentFromUrlWithDigestAuth($url, $username, $password)
    {
        $ch = curl_init();

        $httpCode = '';
        $maxRedirects = 0;
        $redirectsAllowed = 3;

        do {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            if (!empty($username) && !empty($password)) {
                curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");
            }
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $content = curl_exec($ch);

            $response = explode("\r\n\r\n", $content);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 404) {
                return false;
            }

            $content = $response[count($response) -1];

            if ($httpCode == 301 || $httpCode == 302) {
                $matches = array();
                preg_match('/(Location:|URI:)(.*?)\n/', $response[0], $matches);
                $url = trim(array_pop($matches));
            }

            $maxRedirects++;

        } while ($httpCode == 302 ||
                 $httpCode == 301 ||
                 $maxRedirects > $redirectsAllowed
        );

        curl_close($ch);

        return $content;
    }
}
