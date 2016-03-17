<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Onm\Framework\Controller\Controller;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays static pages.
 */
class StaticPageController extends Controller
{
    /**
     * Displays a static page.
     *
     * @param string $slug The static page slug.
     *
     * @return Response The response object.
     */
    public function showAction($slug)
    {
        $oql = 'content_type_name = "static_page" and slug = "%s"';

        $content = $this->get('orm.manager')->getRepository('Content')
            ->findOneBy(sprintf($oql, $slug));

        // If static page does not exist or is not published
        if (empty($content) || !$content->content_status) {
            throw new ResourceNotFoundException();
        }

        // TODO: Remove when pk_content column renamed to id
        $content->id = $content->pk_content;

        $this->view = new \Template(TEMPLATE_USER);
        return $this->render(
            'static_pages/statics.tpl',
            [
                'page'               => $content,
                'content'            => $content,
                'actual_category'    => $content->slug,
                'category_real_name' => $content->title,
                'content_id'         => $content->id,
                'advertisements'     => $this->getAds(),
            ]
        );
    }

    /**
     * Returns all the advertisements for an static page.
     *
     * @return array A list of Advertisements.
     */
    public function getAds()
    {
        // Get static_pages positions
        $manager   = $this->get('instance')->theme->getAdsPositionManager();
        $positions = $manager
            ->getAdsPositionsForGroup('article_inner', [ 1, 2, 5, 6, 7, 9 ]);

        return \Advertisement::findForPositionIdsAndCategory($positions, 0);
    }
}
