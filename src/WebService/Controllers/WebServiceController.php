<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Controllers;

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
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        require_once SITE_VENDOR_PATH.'/Restler/restler.php';
        require_once SITE_VENDOR_PATH.'/Restler/xmlformat.php';

        // Change the request uri to trick Restler
        $_SERVER['REQUEST_URI'] = str_replace('/ws', '', $_SERVER['REQUEST_URI']);

        $r = new \Restler();
        $r->setSupportedFormats('JsonFormat', 'XmlFormat');
        $r->addAPIClass('Instances');
        $r->addAPIClass('Ads');
        $r->addAPIClass('Contents');
        $r->addAPIClass('Articles');
        $r->addAPIClass('Opinions');
        $r->addAPIClass('Comments');
        $r->addAPIClass('Images');
        $r->addAPIClass('Videos');
        $r->addAPIClass('Categories');
        $r->addAPIClass('Authors');
        $r->addAPIClass('Frontpages');

        $r->handle();
        die();
    }
}
