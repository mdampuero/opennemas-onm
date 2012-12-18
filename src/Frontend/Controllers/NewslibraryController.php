<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class NewslibraryController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->ccm = new \ContentCategoryManager();

        // Fetch HTTP variables
        $this->categoryName  = $this->get('request')->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->date          = $this->get('request')->query->filter('date', '', FILTER_SANITIZE_STRING);
        $this->page          = $this->get('request')->query->getDigits('page', 1);

        if (!empty($this->categoryName) || ($this->categoryName == 'home')) {
            $this->category = 0;
            $this->categoryName = 'home';
        } else {
            $this->category = $this->ccm->get_id($this->categoryName);
        }

        $this->view->assign(
            array(
                'newslibraryDate' => $this->date,
                'category_name'   => $this->categoryName,
            )
        );
    }

    /**
     * Calls newslibrary function whether module is activated
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        if (\Onm\Module\ModuleManager::isActivated('FRONTPAGES_LIBRARY')) {
            $this->getFrontpageNewslibrary();
        } elseif (\Onm\Module\ModuleManager::isActivated('STATIC_LIBRARY')) {
            $this->getNewslibraryFile();
        } else {
            $this->getNewslibraryContents();
        }
    }

    /**
     * Get newslibrary from frontpage table database
     *
     * @return Response the response object
     **/
    public function getFrontpageNewslibrary()
    {

        $tpl->setConfig('newslibrary');
        $cacheID = $this->view->generateCacheId($this->date.'_'.$this->categoryName, '', $this->page);

        if ( ($this->view->caching == 0)
           || (!$this->view->isCached('frontpage/fp_newslibrary.tpl', $cacheID))) {

            $frontpage = new Frontpage();

            //TODO: review this option
            if ($frontpage->getFrontpage($this->date, $this->category)) {

                $articles_home = array();
                if (!empty($frontpage->contents)) {
                    foreach ($frontpage->contents as $element) {
                        $content = new $element['content_type']($element['pk_fk_content']);

                        // add all the additional properties related with positions and params
                        $placeholder = ($actual_category_id == 0) ? 'home_placeholder': 'placeholder';
                        $content->load(
                            array(
                                $placeholder => $element['placeholder'],
                                'position'   => $element['position'],
                                'type'       => $element['content_type'],
                                'params'     => unserialize($element['params']),
                            )
                        );

                        if (!empty($content->fk_video)) {
                            $content->video = new Video($content->fk_video);

                        } else {
                            if (!empty($content->img1)) {
                                $content->image = new Photo($content->img1);
                            }
                        }

                        $articles_home[] = $content;
                    }
                }
            }

            $this->view->assign(
                array(
                    'articles_home' => $articles_home,
                )
            );

            require_once APP_PATH.'/../public/controllers/index_advertisement.php';

            return $this->render(
                'frontpage/fp_newslibrary.tpl',
                array(
                    'cache_id' => $cacheID,
                )
            );

        }
    }

    /**
     * Get newslibrary from saved file
     *
     * @return Response the response object
     **/
    public function getNewslibraryFile()
    {
        if (!empty($this->date)) {
            $html = '';
            $path = preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '/$1/$2/$3', $this->date);
            if (file_exists(MEDIA_PATH."/library/{$path}/{$this->categoryName}.html")) {
                $html = file_get_contents(INSTANCE_MEDIA."library/{$path}/{$this->categoryName}.html");
            }

            if (empty($html)) {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }

            $response = new Response($html);
            return $response->send();
        }
    }

    /**
     * Get newslibrary from content table in database
     *
     * @return Response the response object
     **/
    public function getNewslibraryContents()
    {
        $tpl->setConfig('newslibrary');
        $cacheID = $this->view->generateCacheId($this->date.'_'.$this->$this->categoryName, '', $this->page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('frontpage/newslibrary.tpl', $cacheID))) {

            $fp = new \Frontpage();
            $cm = new \ContentManager();
            $allCategories = $this->ccm->categories;

            $library  = array();
            $contents = $this->cm->getContentsForLibrary($this->date);

            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $categoryID = $content->category;
                    if (!isset($library[$categoryID])) {
                        $library[$categoryID] = new stdClass();
                    }
                    $library[$categoryID]->id         = $categoryID;
                    $library[$categoryID]->title      = $allCategories[$categoryID]->title;
                    $library[$categoryID]->contents[] = $content;
                }
            }

            $this->view->assign('library', $library);
        }

        require_once APP_PATH.'/../public/controllers/index_advertisement.php';

        return $this->render(
            'frontpage/fp_list_contents.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }
}
