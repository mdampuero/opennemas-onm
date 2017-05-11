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

use Common\Core\Controller\Controller;
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
        $oql = 'content_type_name = "static_page"'
            . ' and slug = "%s" and content_status = "1" and in_litter = "0"'
            . ' order by pk_content desc';

        try {
            $content = $this->get('orm.manager')->getRepository('Content')
                ->findOneBy(sprintf($oql, $slug));
        } catch (\Exception $e) {
            // If static page does not exist or is not published raise an error
            throw new ResourceNotFoundException();
        }

        // TODO: Remove when pk_content column renamed to id
        $content->id = $content->pk_content;

        list($positions, $advertisements) = $this->getAds();

        return $this->render('static_pages/statics.tpl', [
            'ads_positions'      => $positions,
            'advertisements'     => $advertisements,
            'category_real_name' => $content->title,
            'content'            => $content,
            'content_id'         => $content->id,
            'page'               => $content,
            'x-tags'             => 'static-page,'.$content->id,
        ]);
    }

    /**
     * Returns all the advertisements for an static page.
     *
     * @return array A list of Advertisements.
     */
    public function getAds()
    {
        // Get static_pages positions
        $positionManager = getService('core.helper.advertisement');
        $positions = $positionManager
            ->getPositionsForGroup('article_inner', [ 1, 2, 5, 6, 7 ]);

        $advertisements = \Advertisement::findForPositionIdsAndCategoryPlain($positions, 0);

        return [ $positions, $advertisements ];
    }
}
