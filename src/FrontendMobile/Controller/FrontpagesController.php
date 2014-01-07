<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace FrontendMobile\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package FrontendMobile_Controllers
 **/
class FrontpagesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        define('BASE_PATH', '/mobile');
    }

    /**
     * Displays the mobile frontpage
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->view->setConfig('frontpage-mobile');


        //Get category vars
        $categoryName = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page = $request->query->getDigits('page', 1);

        $cacheID = $this->view->generateCacheId($categoryName, '', 0);
        if (($this->view->caching == 0)
            || !$this->view->isCached('mobile/frontpage-mobile.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();
            $cm = new \ContentManager();

            $this->view->assign('section', $categoryName);

            // TODO: Get rid of this when posible
            require __DIR__.'/../sections.php';

            // Fetch news
            if ($categoryName == 'home') {
                $actualCategoryId = 0;
            } else {
                $actualCategoryId = $ccm->get_id($categoryName);
            }

            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);
            $contentsInHomepage = $cm->getInTime($contentsInHomepage);
            $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'starttime');

            // Invert array order for getting newest first
            $contentsInHomepage = array_reverse($contentsInHomepage);

            // Deleting Widgets and get authors slug's for opinions
            $contents = array();
            $photoIds   = array();
            foreach ($contentsInHomepage as $content) {
                if ($content->content_type != 'Widget'
                    && $content->content_type != '2'
                ) {
                    // If the content is an opinion fetch its authors slug's
                    if ($content->content_type == 4) {
                        $content->author_name_slug = \StringUtils::get_title($content->author);
                    }

                    if (isset($content->img1) && !empty($content->img1)) {
                        $photoIds[] = (int) $content->img1;
                    } elseif (isset($content->img2) && !empty($content->img2)) {
                        $photoIds[] = (int) $content->img2;
                    }

                    $contents[] = $content;
                }
            }

            // Fetch the array of images
            $photosContents = array();
            if (count($photoIds) > 0) {
                $photos = $cm->find('Photo', 'pk_content IN ('. implode(',', $photoIds) .')');

                foreach ($contents as $content) {
                    if ((isset($content->img1)  && !empty($content->img1))
                        || (isset($content->img2) && !empty($content->img2))
                    ) {
                        // Search the images and get path
                        foreach ($photos as $photo) {
                            if ($photo->pk_content == $content->img1 || $photo->pk_content == $content->img2) {
                                // Use thumbnails
                                $photosContents[$content->id] = $photo->path_file . $photo->name;
                                break;
                            }
                        }
                    }
                }
            }

            $this->view->assign(
                array(
                    'ccm'            => $ccm,
                    'photosArticles' => $photosContents,
                    'articles_home'  => $contents
                )
            );
        }

        return $this->render(
            'mobile/frontpage-mobile.tpl',
            array('cache_id' => $cacheID )
        );
    }

    /**
     * Displays the latest news
     *
     * @return Response the response object
     **/
    public function latestNewsAction(Request $request)
    {
        $cm  = new \ContentManager();
        $ccm = \ContentCategoryManager::get_instance();

        $categoryName    = 'ultimas';

        // TODO: Get rid of this when posible
        require __DIR__.'/../sections.php';

        $contents = $cm->find(
            'Article',
            'available=1 AND content_status=1 AND fk_content_type=1',
            'ORDER BY created DESC, changed DESC LIMIT 20'
        );

        //Filter by scheduled
        $contents = $cm->getInTime($contents);

        // Load category for articles
        $photoIds = array();
        foreach ($contents as &$content) {
            $content->category_name = $content->loadCategoryName($content->id);

            if (isset($content->img1) && !empty($content->img1)) {
                $photoIds[] = $content->img1;
            } elseif (isset($content->img2) && !empty($content->img2)) {
                $photoIds[] = $content->img2;
            }
        }

        // Fetch the array of images
        $photos = array();
        if (count($photoIds) > 0) {
            $photos = $cm->find('Photo', 'pk_content IN ('. implode(',', $photoIds) .')');

            foreach ($contents as $content) {
                if ((isset($content->img1)  && !empty($content->img1))
                    || (isset($content->img2) && !empty($content->img2))
                ) {
                    // Search the images and get path
                    foreach ($photos as $photo) {
                        if ($photo->pk_content == $content->img1 || $photo->pk_content == $content->img2) {
                            $photosContents[$content->id] = $photo->path_file . $photo->name;
                            break;
                        }
                    }
                }
            }
        }

        return $this->render(
            'mobile/frontpage-mobile.tpl',
            array(
                'photosArticles' => $photosContents,
                'ccm'            => $ccm,
                'articles_home'  => $contents,
                'section'        => 'ultimas'
            )
        );
    }

    /**
     * Redirects the user to the complete web
     *
     * @return Response the response object
     **/
    public function redirectCompleteWebAction(Request $request)
    {
        setcookie("confirm_mobile", "1", time()+3600, '/');

        return $this->redirect($this->generateUrl('frontend_frontpage'));
    }
}
