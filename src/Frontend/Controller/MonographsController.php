<?php
/**
 * Handles the actions for monographs
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for monographs
 *
 * @package Frontend_Controllers
 */
class MonographsController extends Controller
{
    /**
     * Common code for all the actions
     */
    public function init()
    {
        if (!$this->get('core.security')->hasExtension('SPECIAL_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $this->category     = 0;
        $this->categoryName = $this->get('request_stack')
            ->getCurrentRequest()
            ->query->get('category_slug', '');

        if (!empty($this->categoryName)) {
            $category = $this->get('api.service.category')
                ->getItemBySlug($this->categoryName);

            $this->category = $category->id;
        }

        $this->view->assign([
            'category' => $this->category,
        ]);
    }

    /**
     * Shows the monographs frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function frontpageAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $epp  = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_in_blog', 10);
        $epp  = (is_null($epp) || $epp <= 0) ? 10 : $epp;

        if (empty($this->categoryName)) {
            $this->categoryName = 'home';
        }

        // Setup templating cache layer
        $this->view->setConfig('specials');
        $cacheID = $this->view->getCacheId('frontpage', 'special', $this->categoryName, $page);

        // Don't execute the action logic if was cached before
        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('special/special_frontpage.tpl', $cacheID))
        ) {
            $em = $this->get('entity_repository');

            $order   = [ 'starttime' => 'DESC' ];
            $filters = [
                'content_type_name' => [[ 'value' => 'special' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_home'           => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'starttime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            if ($this->category != 0) {
                $filters['category_id'] = [ [ 'value' => $this->category ] ];
            }

            $monographs = $em->findBy($filters, $order, $epp, $page);
            $total      = count($monographs) + 1;

            $tagsIds = [];
            if (!empty($monographs)) {
                foreach ($monographs as &$monograph) {
                    $tagsIds = array_merge($monograph->tags, $tagsIds);
                }

                $pagination = $this->get('paginator')->get([
                    'directional' => true,
                    'epp'         => $epp,
                    'maxLinks'    => 0,
                    'page'        => $page,
                    'total'       => $total + 1,
                    'route'       => [
                        'name'   => 'frontend_monograph_frontpage'
                    ]
                ]);

                $this->view->assign([
                    'specials'   => $monographs,
                    'pagination' => $pagination,
                    'tags'       => $this->get('api.service.tag')
                        ->getListByIdsKeyMapped(array_unique($tagsIds))['items']
                ]);
            }
        }

        return $this->render('special/frontpage_special.tpl', [
            'cache_id'    => $cacheID,
            'x-tags'      => 'monograph-frontpage',
            'x-cacheable' => true,
        ]);
    }

    /**
     * Shows a monograph
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function showAction(Request $request)
    {
        $dirtyID      = $request->get('special_id', '');
        $urlSlug      = $request->get('slug', '');
        $categoryName = $request->get('category_slug', '');

        $special = $this->get('content_url_matcher')
            ->matchContentUrl('special', $dirtyID, $urlSlug, $categoryName);

        if (empty($special)) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('specials');
        $cacheID = $this->view->getCacheId('content', $special->id);

        if (($this->view->getCaching() === 0)
            || (!$this->view->isCached('special/special.tpl', $cacheID))
        ) {
            $contents = $special->getContents($special->id);
            $columns  = [];

            if (!empty($contents)) {
                foreach ($contents as $item) {
                    $content = $this->get('entity_repository')->find($item['type_content'], $item['fk_content']);

                    if (($item['position'] % 2) == 0) {
                        $content->placeholder = 'placeholder_0_1';
                    } else {
                        $content->placeholder = 'placeholder_1_1';
                    }

                    $columns[] = $content;
                }
            }

            $this->view->assign(['columns' => $columns]);
        }

        return $this->render('special/special.tpl', [
            'special'     => $special,
            'content'     => $special,
            'contentId'   => $special->id,
            'cache_id'    => $cacheID,
            'o_content'   => $special,
            'x-tags'      => 'monograph,' . $special->id,
            'x-cacheable' => true,
            'tags'        => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($special->tags)['items']
        ]);
    }
}
