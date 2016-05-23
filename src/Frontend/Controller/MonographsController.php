<?php
/**
 * Handles the actions for monographs
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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for monographs
 *
 * @package Frontend_Controllers
 **/
class MonographsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);

        // Setting up available categories for menu.
        $this->ccm = new \ContentCategoryManager();
        $this->cm  = new \ContentManager();

        $this->categoryName = $this->get('request')->query
            ->filter('category_name', '', FILTER_SANITIZE_STRING);

        if (!empty($this->categoryName)) {
            $this->category     = $this->ccm->get_id($this->categoryName);
            $actual_category_id = $this->category;
            $category_real_name = $this->ccm->getTitle($this->categoryName);
        } else {
            $category_real_name = 'Portada';
            $this->category     = 0;
            $actual_category_id = 0;
        }

        $this->view->assign(
            array(
                'category_name'         => $this->categoryName,
                'category'              => $this->category,
                'actual_category'       => $this->categoryName,
                'actual_category_id'    => $actual_category_id,
                'actual_category_title' => $category_real_name,
                'category_real_name'    => $category_real_name,
            )
        );
    }

    /**
     * Shows the monographs frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $this->page = $request->query->getDigits('page', 1);

        // Setup caching system
        $this->view->setConfig('special_frontpage');
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);

        // Don't execute the action logic if was cached before
        if (($this->view->caching == 0)
           || (!$this->view->isCached('special/special_frontpage.tpl', $cacheID))
        ) {
            if (isset($this->category) && !empty($this->category)) {
                $monographs = $this->cm->find_by_category(
                    'Special',
                    $this->category,
                    'content_status=1',
                    ' ORDER BY starttime DESC LIMIT 14'
                );
            } else {
                $monographs = $this->cm->find(
                    'Special',
                    'content_status=1',
                    ' ORDER BY starttime DESC LIMIT 14'
                );
            }

            if (!empty($monographs)) {
                foreach ($monographs as &$monograph) {
                    if (!empty($monograph->img1)) {
                        $img = $this->get('entity_repository')->find('Photo', $monograph->img1);
                        $monograph->img1_path = $img->path_file.$img->name;
                        $monograph->img       = $img;
                    }
                    $monograph->category_name  = $monograph->loadCategoryName($monograph->id);
                    $monograph->category_title = $monograph->loadCategoryTitle($monograph->id);
                }

                $this->view->assign(
                    array(
                        'specials' => $monographs
                    )
                );
            }
        }

        return $this->render(
            'special/frontpage_special.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Shows a monograph
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->filter('special_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $special = $this->get('content_url_matcher')
            ->matchContentUrl('special', $dirtyID, $urlSlug);

        if (empty($special)) {
            throw new ResourceNotFoundException();
        }

        $cacheID = $this->view->generateCacheId($this->categoryName, null, $special->id);
        if (($this->view->caching == 0)
            || (!$this->view->isCached('special/special.tpl', $cacheID))
        ) {
            $contents = $special->getContents($special->id);
            $columns  = array();

            $er = $this->get('entity_repository');
            if (!empty($contents)) {
                if ((count($contents) == 1)  &&
                    (($contents[0]['type_content']=='Attachment')
                    || ($contents[0]['type_content']=='3'))
                ) {
                    $content = \Content::get($contents[0]['fk_content']);

                    $special->pdf_path = $content->path;
                } else {
                    foreach ($contents as $item) {
                        $content = \Content::get($item['fk_content']);

                        if (!empty($content->img1)) {
                            $photo = $er->find('Photo', $content->img1);
                            $content->img1_path = $photo->path_file.$photo->name;
                            $content->img1      = $photo;
                        }

                        if (!empty($content->fk_video)) {
                            $video = $er->find('Video', $content->fk_video);
                            $content->obj_video = $video;
                        }

                        if (($item['position'] % 2) == 0) {
                            $content->placeholder = 'placeholder_0_1';
                        } else {
                            $content->placeholder = 'placeholder_1_1';
                        }

                        $content->category_name  = $content->loadCategoryName($item['fk_content']);
                        $content->category_title = $content->loadCategoryTitle($item['fk_content']);

                         // Load attached and related contents from array
                        $content->loadAttachedVideo()
                                ->loadRelatedContents($this->categoryName);

                        $columns[] = $content;
                    }
                }
            }

            if (!empty($special->img1)) {
                $photo = $er->find('Photo', $special->img1);
                $special->path_img = $photo->path_file.$photo->name;
                $special->img      = $photo;
            }

            $this->view->assign(
                array(
                    'special'   => $special,
                    'content'   => $special,
                    'columns'   => $columns,
                    'contentId' => $special->id,
                )
            );
        }

        return $this->render(
            'special/special.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }
}
