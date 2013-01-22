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
 * Handles the actions for newslibrary
 *
 * @package Frontend_Controllers
 **/
class ArchiveController extends Controller
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
        $this->page          = $this->get('request')->query->getDigits('page', 1);
        $this->year          = $this->get('request')->query->filter('year', '', FILTER_SANITIZE_STRING);
        $this->month         = $this->get('request')->query->filter('month', '', FILTER_SANITIZE_STRING);
        $this->day           = $this->get('request')->query->filter('day', '', FILTER_SANITIZE_STRING);
        //$this->date          = $this->get('request')->query->filter('date', '', FILTER_SANITIZE_STRING);
        $this->date          = "{$this->year}-{$this->month}-{$this->day}";

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
     * Get newslibrary from content table in database
     *
     * @return Response the response object
     **/
    public function archiveAction()
    {
        $this->view->setConfig('newslibrary');
        $cacheID = $this->view->generateCacheId($this->date.'_'.$this->categoryName, '', $this->page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/newslibrary.tpl', $cacheID))) {

            $fp = new \Frontpage();
            $cm = new \ContentManager();
            $allCategories = $this->ccm->categories;

            $library  = array();
            $contents = $cm->getContentsForLibrary($this->date);

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

        $this->getAds();

        return $this->render(
            'archive/list_contents.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Get newslibrary from content table in database
     *
     * @return Response the response object
     **/
    public function archiveCategoryAction()
    {
        $this->view->setConfig('newslibrary');
        $cacheID = $this->view->generateCacheId($this->date.'_'.$this->categoryName, '', $this->page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/newslibrary.tpl', $cacheID))) {

             $cm = new \ContentManager();

            $library  = array();
            $library[$categoryID] = new stdClass();
            $contents = $cm->getContentsForLibrary($this->date, $category);

            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $library[$categoryID]->id         = $categoryID;
                    $library[$categoryID]->title      = $categoryName;
                    $library[$categoryID]->contents[] = $content;
                }
            }

            $this->view->assign('library', $library);
        }

        $this->getAds();

        return $this->render(
            'archive/list_contents.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Get frontpage version from file
     *
     * @return Response the response object
     **/
    public function digitalFrontpageAction()
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
}

