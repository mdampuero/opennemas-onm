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

class RobotsController extends Controller
{
    /**
     * Displays a prebuilt robots.txt file
     *
     * @return Response The response object.
     */
    public function indexAction()
    {
        $disableRobots = $this->getParameter('disable_robots');
        $siteUrl       = $this->get('core.instance')->getBaseUrl();

        $rules = $this->get('orm.manager')->getDataSet('Settings')
            ->get('robots_txt_rules', '');

        $content = "User-Agent: *\n"
            . "Disallow: /harming/humans\n"
            . "Disallow: /ignoring/human/orders\n"
            . "Disallow: /harm/to/self\n"
            . "Disallow: /api\n"
            . "Disallow: " . ($disableRobots ? "/" : "/admin") . "\n"
            . (!empty($rules) ? "\n" . $rules : "") . "\n"
            . "\n"
            . "Sitemap: " . $siteUrl . "/sitemap.latest.xml.gz\n"
            . "Sitemap: " . $siteUrl . "/sitemap.article.xml.gz\n"
            . "Sitemap: " . $siteUrl . "/sitemap.opinion.xml.gz\n"
            . "Sitemap: " . $siteUrl . "/sitemap.video.xml.gz\n"
            . "Sitemap: " . $siteUrl . "/sitemap.image.xml.gz";

        return new Response($content, 200, [
            'Content-Type' => 'text/plain',
            'x-cacheable'  => true,
            'x-tags'       => 'robots',
        ]);
    }
}
