<?php
/**
 * Defines the frontend controller for the advertisement content type
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Defines the frontend controller for the advertisement content type
 *
 * @package Frontend_Controllers
 */
class AdvertisementController extends Controller
{
    /**
     * Displays an advertisement given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function getAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $ad = $this->get('entity_repository')->find('Advertisement', $id);

        if (!is_object($ad)) {
            throw new ResourceNotFoundException();
        }

        return $this->render('ads/advertisement.tpl', [
            'banner'  => $ad,
            'content' => $ad,
            'x-tags' => 'ad,' . $id
        ]);
    }

    /**
     * Redirects the user to the target URL defined by an advertisement
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function redirectAction(Request $request)
    {
        $dirtyID = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        // Resolve ad ID, search in repository or redirect to 404
        $advertisement = $this->get('content_url_matcher')
            ->matchContentUrl('advertisement', $dirtyID);

        if (empty($advertisement)) {
            throw new ResourceNotFoundException();
        }

        // Increase number of clicks
        $advertisement->setNumClics($advertisement->id);

        if ($advertisement->url) {
            return $this->redirect($advertisement->url);
        } else {
            return new Response('<script type="text/javascript">window.close();</script>');
        }
    }

    /**
     * Displays a public record of Authorized Digital Sellers - ads.txt file
     *
     * @return Response The response object.
     */
    public function adsTxtAction()
    {
        // Check for the module existence and if it is enabled
        if (!$this->get('core.security')->hasExtension('es.openhost.module.advancedAdvertisement')) {
            throw new ResourceNotFoundException();
        }

        $content      = $this->get('setting_repository')->get('ads_txt');
        $instanceName = getService('core.instance')->internal_name;

        return new Response($content, 200, [
            'Content-Type' => 'text/plain',
            'x-cacheable'  => true,
            'x-cache-for'  => '100d',
            'x-tags'       => 'instance-' . $instanceName . ',ads,txt',
            'x-instance'   => $instanceName,
        ]);
    }
}
