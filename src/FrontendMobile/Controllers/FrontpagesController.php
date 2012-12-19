<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace FrontendMobile\Controllers;

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
    }

    /**
     * Displays the mobile frontpage
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->view->setConfig('frontpage-mobile');

        $url = $request->query->filter('url', '', FILTER_SANITIZE_URL);

        //Get category vars
        $category_name = isset($sections[1])? $sections[1] : null;
        $subcategory_name = filter_input(INPUT_GET, 'subcategory_name', FILTER_SANITIZE_STRING);
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        list($category_name, $subcategory_name) = $ccm->normalize($category_name, $subcategory_name);

        if (($category_name!='home') && ($category_name!='')) {
            if ($ccm->isEmpty($category_name) && is_null($subcategory_name)) {
                $subcategory_name = $ccm->getFirstSubcategory($ccm->get_id($category_name));
                if (is_null($subcategory_name)) {
                    \Application::forward301('/mobile/');
                } else {
                    \Application::forward301('/mobile/seccion/'.$category_name.'/'.$subcategory_name.'/');
                }
            }
        }

        $this->view->assign('ccm', $ccm);

        $cacheID = $this->view->generateCacheId($category_name, $subcategory_name, 0);

        if (($this->view->caching == 0) || !$this->view->isCached('mobile/frontpage-mobile.tpl', $cacheID)) {

            $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
            $section = (is_null($section))? 'home': $section;
            $this->view->assign('section', $section);
            //$this->view->loadConfigOrDefault('template.conf', $section); // $category_name is a string

            //Get Content manager instance
            $cm = new \ContentManager();

            // Get rid of this when posible
            // include 'sections.php';

            // Fetch news
            if ($section == 'home') {
                $actualCategoryId = 0;
                $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);
                // Filter articles if some of them has time scheduling and sort them by position
                $contentsInHomepage = $cm->getInTime($contentsInHomepage);
                $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'starttime');
            } else {
                $this->view->assign('section', $category_name);
                $actualCategoryId =  $ccm->get_id($section);
                $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);
                // Filter articles if some of them has time scheduling and sort them by position
                $contentsInHomepage = $cm->getInTime($contentsInHomepage);
                $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'starttime');
            }

            // Invert array order to put newest first
            $contentsInHomepage = array_reverse($contentsInHomepage);

            // Deleting Widgets and get authors slug's for opinions
            $articles_home = array();
            foreach ($contentsInHomepage as $content) {
                if (isset($content->home_placeholder)
                    && !empty($content->home_placeholder)
                    && ($content->home_placeholder != '')
                    && ($content->content_type != 'Widget')
                    && ($content->content_type != '2')
                ) {
                    if ($content->content_type == 4) {
                        //Fetch authors slug's
                        $content->author_name_slug = \StringUtils::get_title($content->author);
                    }

                    $articles_home[] = $content;
                }
            }
            $this->view->assign('articles_home', $articles_home);

            // Get frontpage article image id, if not get inner image id
            $imagenes = array();
            foreach ($articles_home as $art) {
                if (isset($art->img1) && !empty($art->img1)) {
                    $imagenes[] = $art->img1;
                } elseif (isset($art->img2) && !empty($art->img2)) {
                    $imagenes[] = $art->img2;
                }
            }

            // Fetch the array of images
            if (count($imagenes)>0) {
                $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');

                $photos = array();
                foreach ($articles_home as $art) {
                    if ((isset($art->img1)  && !empty($art->img1))
                        || (isset($art->img2) && !empty($art->img2))) {
                        // Search the images and get path
                        foreach ($imagenes as $img) {
                            if ($img->pk_content == $art->img1 || $img->pk_content == $art->img2) {
                                // Use thumbnails
                                $photos[$art->id] = $img->path_file . $img->name;
                                break;
                            }
                        }
                    }
                }
                $this->view->assign('photosArticles', $photos);
            }
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
        //Is category initialized redirect the user to /
        $category_name    = 'ultimas';
        $subcategory_name = null;
        $page = $_GET['page'] = 0;

        $ccm = \ContentCategoryManager::get_instance();
        $this->view->assign('ccm', $ccm);

        //Get rid of this when posible
        // require_once 'sections.php';

        $section = (!empty($subcategory_name))? $subcategory_name: $category_name;
        $section = (is_null($section))? 'home': $section;
        $this->view->assign('section', $section);
        //$this->view->loadConfigOrDefault('template.conf', $section); // $category_name is a string

        //Get content manager instance
        $cm = new \ContentManager();

        $articles_home = $cm->find(
            'Article',
            'available=1 AND content_status=1 AND fk_content_type=1',
            'ORDER BY created DESC, changed DESC LIMIT 0, 20'
        );
        if (empty($articles_home)) {

            //Fetching content
            $contentsInHomepage = $cm->getContentsForHomepageOfCategory(0);

            //Deleting widgets
            foreach ($contentsInHomepage as $content) {
                if (isset($content->home_placeholder)
                   && !empty($content->home_placeholder)
                   && ($content->home_placeholder != '')
                   && ($content->content_type == 4)
                ) {
                    $articles_home[] = $content;
                }
            }
        }
        //Filter by scheduled
        $articles_home = $cm->getInTime($articles_home);

        //Load category for articles
        foreach ($articles_home as $i => $article) {
            $articles_home[$i]->category_name = $articles_home[$i]->loadCategoryName($articles_home[$i]->id);
        }

        $this->view->assign('articles_home', $articles_home);

        //Get frontpage article image id, if not get inner image id
        $imagenes = array();
        foreach ($articles_home as $art) {
            if (isset($art->img1) && !empty($art->img1)) {
                $imagenes[] = $art->img1;
            } elseif (isset($art->img2) && !empty($art->img2)) {
                $imagenes[] = $art->img2;
            }
        }

        //Fetch the array of images
        if (count($imagenes)>0) {
            $imagenes = $cm->find('Photo', 'pk_content IN ('. implode(',', $imagenes) .')');

            $photos = array();
            foreach ($articles_home as $art) {
                if ((isset($art->img1)  && !empty($art->img1))
                    || (isset($art->img2) && !empty($art->img2))) {
                    // Search the images and get path
                    foreach ($imagenes as $img) {
                        if ($img->pk_content == $art->img1 || $img->pk_content == $art->img2) {
                            // Use thumbnails
                            $photos[$art->id] = $img->path_file . $img->name;
                            break;
                        }
                    }
                }
            }
        }

        // Without cache because is a lastest news section
        return $this->render(
            'mobile/frontpage-mobile.tpl',
            array(
                'photosArticles' => $photos
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
