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
        // Only is used by cronicas, no one has templates to support specials.
        // https://openhost.atlassian.net/browse/ONM-1995
        if (!$this->get('core.security')->hasExtension('SPECIAL_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        // Setting up available categories for menu.
        $this->ccm = new \ContentCategoryManager();
        $this->cm  = new \ContentManager();

        $this->categoryName = $this->get('request_stack')->getCurrentRequest()
            ->query->filter('category_name', '', FILTER_SANITIZE_STRING);

        if (!empty($this->categoryName)) {
            $this->category     = $this->ccm->get_id($this->categoryName);
            $actual_category_id = $this->category;
            $category_real_name = $this->ccm->getTitle($this->categoryName);
        } else {
            $category_real_name = 'Portada';
            $this->category     = 0;
            $actual_category_id = 0;
        }

        $this->view->assign([
            'category_name'         => $this->categoryName,
            'category'              => $this->category,
            'actual_category'       => $this->categoryName,
            'actual_category_id'    => $actual_category_id,
            'actual_category_title' => $category_real_name,
            'category_real_name'    => $category_real_name,
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
        $this->page = $request->query->getDigits('page', 1);

        if (empty($this->categoryName)) {
            $this->categoryName = 'home';
        }

        // Setup templating cache layer
        $this->view->setConfig('specials');
        $cacheID = $this->view->getCacheId('frontpage', 'special', $this->categoryName, $this->page);

        // Don't execute the action logic if was cached before
        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('special/special_frontpage.tpl', $cacheID))
        ) {
            $em = $this->get('entity_repository');

            $order   = [ 'starttime' => 'DESC' ];
            $filters = [
                'content_type_name' => [[ 'value' => 'special' ]],
                'content_status'    => [[ 'value' => 1 ]],
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
                $filters['pk_fk_content_category'] = [ [ 'value' => $this->category ] ];
            }

            $monographs = $em->findBy($filters, $order, 14);

            $tagsIds = [];
            if (!empty($monographs)) {
                foreach ($monographs as &$monograph) {
                    $tagsIds = array_merge($monograph->tag_ids, $tagsIds);
                    if (!empty($monograph->img1)) {
                        $img = $this->get('entity_repository')
                            ->find('Photo', $monograph->img1);
                        // Generate image path
                        $monograph->img1_path = $img->path_file . $img->name;
                        $monograph->img       = $img;
                    }
                }

                $this->view->assign(['specials' => $monographs]);
                $this->view->assign(
                    'tags',
                    $this->get('api.service.tag')
                        ->getListByIdsKeyMapped(array_unique($tagsIds))['items']
                );
            }
        }

        return $this->render('special/frontpage_special.tpl', [
            'cache_id'    => $cacheID,
            'x-tags'      => 'monograph-frontpage',
            'x-cache-for' => '+1 day',
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
        $dirtyID      = $request->query->get('special_id', '');
        $urlSlug      = $request->query->get('slug', '');
        $categoryName = $request->query->get('category_name', '');

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

            $er = $this->get('entity_repository');
            if (!empty($contents)) {
                if ((count($contents) == 1)
                    && (($contents[0]['type_content'] == 'Attachment')
                    || ($contents[0]['type_content'] == '3'))
                ) {
                    $content = \Content::get($contents[0]['fk_content']);

                    $special->pdf_path = $content->path;
                } else {
                    foreach ($contents as $item) {
                        $content = \Content::get($item['fk_content']);

                        if (!empty($content->img1)) {
                            $photo              = $er->find('Photo', $content->img1);
                            $content->img1_path = $photo->path_file . $photo->name;
                            $content->img1      = $photo;
                        }

                        if (!empty($content->fk_video)) {
                            $video              = $er->find('Video', $content->fk_video);
                            $content->obj_video = $video;
                        }

                        if (($item['position'] % 2) == 0) {
                            $content->placeholder = 'placeholder_0_1';
                        } else {
                            $content->placeholder = 'placeholder_1_1';
                        }

                        $content->category_name  = $content->loadCategoryName();
                        $content->category_title = $content->loadCategoryTitle();

                         // Load attached and related contents from array
                        $content->loadAttachedVideo()
                            ->loadRelatedContents($this->categoryName);

                        $columns[] = $content;
                    }
                }
            }

            if (!empty($special->img1)) {
                $photo             = $er->find('Photo', $special->img1);
                $special->path_img = $photo->path_file . $photo->name;
                $special->img      = $photo;
            }

            $this->view->assign(['columns' => $columns]);
        }

        return $this->render('special/special.tpl', [
            'special'     => $special,
            'content'     => $special,
            'contentId'   => $special->id,
            'cache_id'    => $cacheID,
            'x-tags'      => 'monograph,' . $special->id,
            'x-cache-for' => '+1 day',
            'tags'        => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($special->tag_ids)['items']
        ]);
    }
}
