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

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Symfony\Component\HttpFoundation\Request;

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
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function listAction()
    {
        $syncParameters = $this->get('setting_repository')->get('sync_params');

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

        // Get saved settings if exists (update action)
        $siteData = [];
        if ($syncParams = $this->get('setting_repository')->get('sync_params')) {
            $siteData = array_merge($syncParams, [$data['site_url'] => $data]);
        } else {
            $siteData = [$data['site_url'] => $data];
        }

        if ($this->get('setting_repository')->set('sync_params', $siteData)) {
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
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function fetchCategoriesAction(Request $request)
    {
        $siteUrl  = $request->request->filter('site_url', '', FILTER_VALIDATE_URL);
        $username = $request->request->filter('username', '', FILTER_SANITIZE_STRING);
        $password = $request->request->filter('password', '', FILTER_SANITIZE_STRING);

        $element = [];
        $authError = false;
        if (!empty($siteUrl)) {
            $siteUrl = rtrim($siteUrl, '/\\');
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
            $syncParams = $this->get('setting_repository')->get('sync_params');
            // Get site values if exists
            if ($syncParams) {
                foreach ($syncParams as $site => $values) {
                    if (preg_match('@'.$site.'@', $siteUrl)) {
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
        $syncParams = $this->get('setting_repository')->get('sync_params');

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
     * @Security("hasExtension('SYNC_MANAGER')
     *     and hasPermission('INSTANCE_SYNC_ADMIN')")
     */
    public function deleteAction(Request $request)
    {
        $siteUrl = $request->query->filter('site_url', '');

        // Fetch params from db
        $syncParams = $this->get('setting_repository')->get('sync_params');

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

        if ($this->get('setting_repository')->set('sync_params', $syncParams)) {
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
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_HEADER         => true,
            CURLOPT_VERBOSE        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,    // for https
            CURLOPT_USERPWD        => $username . ":" . $password,
            CURLOPT_HTTPAUTH       => CURLAUTH_DIGEST,
        ];
        $ch = curl_init();
        curl_setopt_array( $ch, $options );

        $httpCode = '';
        $maxRedirects = 0;
        $redirectsAllowed = 3;

        do {
            try {
                $content = curl_exec( $ch );

                // validate CURL status
                if (curl_errno($ch)) {
                    throw new \Exception(curl_error($ch), 500);
                }

                // validate HTTP status code (user/password credential issues)
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($httpCode != 200) {
                    throw new \Exception("Response with Status Code [" . $httpCode . "].", 500);
                }
                $response = explode("\r\n\r\n", $content);
                $content = $response[count($response) -1];

                if ($httpCode == 301 || $httpCode == 302) {
                    $matches = array();
                    preg_match('/(Location:|URI:)(.*?)\n/', $response[0], $matches);
                    $url = trim(array_pop($matches));
                }
            } catch(\Exception $ex) {
                if ($ch != null) {
                    curl_close($ch);
                }
                return false;
            }

            $maxRedirects++;
        } while (
            $httpCode == 302 ||
            $httpCode == 301 ||
            $maxRedirects > $redirectsAllowed
        );

        curl_close($ch);

        return $content;
    }
}
