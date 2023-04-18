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
            return new Response('<script>window.close();</script>');
        }
    }

    /**
     * Displays a public record of Authorized Digital Sellers - ads.txt file
     *
     * @return Response The response object.
     */
    public function showAdsTxtAction()
    {
        // Check ads module and show default ads.txt if deactivated
        if (!$this->get('core.security')->hasExtension('ADS_MANAGER')) {
            $content = "google.com, pub-7694073983816204, DIRECT, f08c47fec0942fa0\n"
                . "#SmartAdServer\n"
                . "smartadserver.com,3035,DIRECT\n"
                . "contextweb.com,560288,DIRECT,89ff185a4c4e857c\n"
                . "pubmatic.com,156439,DIRECT\n"
                . "pubmatic.com,154037,DIRECT\n"
                . "rubiconproject.com,16114,DIRECT,0bfd66d529a55807\n"
                . "openx.com,537149888,DIRECT,6a698e2ec38604c6\n"
                . "sovrn.com,257611,DIRECT,fafdf38b16bf6b2b\n"
                . "appnexus.com,3703,DIRECT,f5ab79cb980f11d1";

            return new Response($content, 200, [
                'Content-Type' => 'text/plain',
                'x-cache-for'  => '100d',
                'x-cacheable'  => true,
                'x-tags'       => 'ads,txt',
            ]);
        }

        // Check for the module existence and if it is enabled
        if (!$this->get('core.security')->hasExtension('es.openhost.module.advancedAdvertisement')) {
            throw new ResourceNotFoundException();
        }

        $content = $this->get('core.helper.advertisement')->getAdsTxtContent();

        return new Response(trim($content), 200, [
            'Content-Type' => 'text/plain',
            'x-cache-for'  => '100d',
            'x-cacheable'  => true,
            'x-tags'       => 'ads,txt',
        ]);
    }
}
