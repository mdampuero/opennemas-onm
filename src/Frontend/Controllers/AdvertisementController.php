<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class AdvertisementController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
    }

    /**
     * Displays an advertisement given its id
     *
     * @param integer id the id of the advertisement
     *
     * @return Response the response object
     **/
    public function getAction(Request $request)
    {
        // Banner Id
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $id = \Content::resolveID($id);

        $advertisement = new \Advertisement();
        /* $banner = $advertisement->cache->read($id); */
        $advertisement->setNumClics($id);
        $banner = $advertisement->read($id);

        return $this->render(
            'ads/advertisement.tpl',
            array('banner' => $banner)
        );
    }

    /**
     * Redirects the user to the target url defined by an advertisement
     *
     * @param int id the id of the advertisement
     *
     * @return Response the response object
     **/
    public function redirectAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $id = \Content::resolveID($id);

        $content = '';

        if (isset($id)) {
            $advertisement = new \Advertisement($id);
            $url = $advertisement->getUrl($id);

            $advertisement->setNumClics($id);

            if ($url) {
                return $this->redirect($url);
            } else {
                $content = '<script type="text/javascript">window.close();</script>';
            }
        }

        return new Response($content);
    }
}
