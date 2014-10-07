<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the web service
 *
 * @package Backend_Controllers
 **/
class WebServiceController extends Controller
{
    /**
     * Forwards all the web service requests to Restler
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        require_once SITE_LIBS_PATH.'/Restler/restler.php';
        require_once SITE_LIBS_PATH.'/Restler/xmlformat.php';
        require_once SITE_LIBS_PATH.'/Restler/OnmAuth.php';

        // Change the request uri to trick Restler
        $_SERVER['REQUEST_URI'] = str_replace('/ws', '', $_SERVER['REQUEST_URI']);

        if ($_SERVER['REQUEST_URI'] == '') {
            $_SERVER['REQUEST_URI'] = '/';
        }

        $r = new \Restler();
        $r->container = $this->container;
        $r->setSupportedFormats('JsonFormat', 'XmlFormat');
        $r->addAPIClass('WebService\Handlers\Ads');
        $r->addAPIClass('WebService\Handlers\Agency');
        $r->addAPIClass('WebService\Handlers\Articles');
        $r->addAPIClass('WebService\Handlers\Authors');
        $r->addAPIClass('WebService\Handlers\Categories');
        $r->addAPIClass('WebService\Handlers\Comments');
        $r->addAPIClass('WebService\Handlers\Contents');
        $r->addAPIClass('WebService\Handlers\Frontpages');
        $r->addAPIClass('WebService\Handlers\Images');
        $r->addAPIClass('WebService\Handlers\Instances');
        $r->addAPIClass('WebService\Handlers\Opinions');
        $r->addAPIClass('WebService\Handlers\Videos');

        $r->addAuthenticationClass('OnmAuth');

        $r->handle();
        return;
    }
}
