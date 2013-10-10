<?php
/**
 * Handles the actions for advertisements
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
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function getAction(Request $request)
    {
        $id = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        $er = $this->get('entity_repository');

        $advertisement = $er->find('Advertisement', $id);

        return $this->render(
            'ads/advertisement.tpl',
            array(
                'banner'  => $advertisement,
                'content' => $advertisement
            )
        );
    }

    /**
     * Redirects the user to the target url defined by an advertisement
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
