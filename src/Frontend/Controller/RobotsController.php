<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Common\Core\Controller\Controller;

/**
 * Generates the robots.txt file
 *
 * @package Frontend_Controllers
 */
class RobotsController extends Controller
{
    /**
     * Displays a prebuilt robots.txt file
     *
     * @return Response The response object.
     */
    public function indexAction()
    {
        $disableRobots = $this->container->getParameter('disable_robots');
        $rules         = $this->get('setting_repository')->get('robots_txt_rules');
        $customRules   = (is_string($rules)) ? $rules : '';
        $instanceName  = getService('core.instance')->internal_name;

        $content = "User-Agent: *\n"
            . "Disallow: /harming/humans\n"
            . "Disallow: /ignoring/human/orders\n"
            . "Disallow: /harm/to/self\n"
            . "Disallow: /content/print\n"
            . "Disallow: /content/share-by-email\n"
            . "Disallow: /api\n"
            . "Disallow: " . ($disableRobots ? "/" : "/admin") . "\n"
            . (!empty($customRules) ? "\n" . $customRules : "") . "\n"
            . "\n"
            . "Sitemap: " . SITE_URL . "sitemapnews.xml.gz\n"
            . "Sitemap: " . SITE_URL . "sitemapweb.xml.gz\n"
            . "Sitemap: " . SITE_URL . "sitemapvideo.xml.gz\n"
            . "Sitemap: " . SITE_URL . "sitemapimage.xml.gz";

        return new Response($content, 200, [
            'Content-Type' => 'text/plain',
            'x-cacheable'  => true,
            'x-cache-for'  => '100d',
            'x-tags'       => 'instance-' . $instanceName . ',robots',
            'x-instance'   => $instanceName,
        ]);
    }
}
