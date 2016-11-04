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
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Generates the robots.txt file
 *
 * @package Frontend_Controllers
 **/
class RobotsController extends Controller
{
    /**
     * Displays a prebuilt robots.txt file
     *
     * @return Response the response object
     **/
    public function indexAction()
    {
        $disableRobots = $this->container->getParameter('disable_robots');

        $sm = $this->get('setting_repository');
        $rules = $sm->get(['robots_txt_rules']);
        $customRules = (is_array($rules) && array_key_exists('robots_txt_rules', $rules))
            ? $rules['robots_txt_rules'] : '';
        if ($disableRobots) {
            $content = "User-Agent: *
Disallow: /
";
        } else {
            $content = "User-Agent: *
Disallow: /admin/
Allow: /

Disallow: /harming/humans
Disallow: /ignoring/human/orders
Disallow: /harm/to/self

{$customRules}

Sitemap: ".SITE_URL."sitemapnews.xml.gz
Sitemap: ".SITE_URL."sitemapweb.xml.gz
Sitemap: ".SITE_URL."sitemapvideo.xml.gz
Sitemap: ".SITE_URL."sitemapimage.xml.gz
";
        }

        $instanceName = getService('core.instance')->internal_name;

        return new Response(
            $content,
            200,
            array(
                'Content-Type' => 'text/plain',
                'x-cacheable'  => true,
                'x-cache-for'  => '+100 day',
                'x-tags'       =>  'instance-'.$instanceName.',robots',
                'x-instance'   => $instanceName,
            )
        );
    }
}
