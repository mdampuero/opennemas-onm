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
use Onm\Message as m;
use Onm\Settings as s;

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

        // Returns the HTML for the add and a header to varnish
        $this->view = new \Template(TEMPLATE_USER);
        return $this->render(
            'ads/advertisement.tpl',
            array(
                'banner'  => $advertisement,
                'content' => $advertisement
            ),
            new Response('', 200, array('x-tags' => "ad,$id"))
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
        $id = \Content::resolveID($id);

        $content = '';

        if (isset($id)) {
            $er = $this->get('entity_repository');

            $advertisement = $er->find('Advertisement', $id);
            $advertisement->setNumClics($id);

            if ($advertisement->url) {
                return $this->redirect($advertisement->url);
            } else {
                $content = '<script type="text/javascript">window.close();</script>';
            }
        }

        return new Response($content);
    }
}
