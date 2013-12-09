<?php
/**
 * Generates the robots.txt file
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
 * Generates the robots.txt file
 *
 * @package Frontend_Controllers
 **/
class RobotsController extends Controller
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
     * Displays a prebuild robots.txt file
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function indexAction(Request $request)
    {
        $content = "User-Agent: *
Disallow: /admin/
Allow: /

Disallow: /harming/humans
Disallow: /ignoring/human/orders
Disallow: /harm/to/self

Sitemap: ".SITE_URL."sitemapnews.xml.gz
Sitemap: ".SITE_URL."sitemapweb.xml.gz
";
        return new Response(
            $content,
            200,
            array(
                'Content-Type' => 'text/plain',
                'x-tags'       => 'robots'
            )
        );
    }
}
