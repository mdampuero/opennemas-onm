<?php
/**
 * Defines the frontend controller for the advertisement content type
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Defines the frontend controller for the advertisement content type
 *
 * @package Frontend_Controllers
 **/
class AdvertisementController extends Controller
{
    /**
     * Displays an advertisement given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function getAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        $advertisement = $this->get('entity_repository')->find('Advertisement', $id);

        if (!is_object($advertisement)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        // Returns the HTML for the add and a header to varnish
        $this->view = new \Template(TEMPLATE_USER);
        return $this->render(
            'ads/advertisement.tpl',
            [
                'banner'  => $advertisement,
                'content' => $advertisement,
                'x-tags' => 'ad,'.$id
            ]
        );
    }

    /**
     * Redirects the user to the target URL defined by an advertisement
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function redirectAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        // Resolve ad ID, search in repository or redirect to 404
        list($id, $urlDate) = \ContentManager::resolveID($id);
        $advertisement = $this->get('entity_repository')->find('Advertisement', $id);
        if (!\ContentManager::checkValidContentAndUrl($advertisement, $urlDate)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        // Increase number of clicks
        $advertisement->setNumClics($id);

        if ($advertisement->url) {
            return $this->redirect($advertisement->url);
        } else {
            return new Response('<script type="text/javascript">window.close();</script>');
        }
    }
}
