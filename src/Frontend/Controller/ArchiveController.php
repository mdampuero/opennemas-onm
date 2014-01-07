<?php
/**
 * Handles the actions for newslibrary
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
        $this->request = $this->get('request');
        $today = new \DateTime();
        $today->modify('-1 day');
        // Fetch HTTP variables
        $this->categoryName  = $this->request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->page          = $this->request->query->getDigits('page', 1);
        $this->year          = $this->request->query->filter('year', $today->format('Y'), FILTER_SANITIZE_STRING);
        $this->month         = $this->request->query->filter('month', $today->format('m'), FILTER_SANITIZE_STRING);
        $this->day           = $this->request->query->filter('day', $today->format('d'), FILTER_SANITIZE_STRING);
    }


    /**
     * Get newslibrary from content table in database
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function archiveAction(Request $request)
    {
        $this->view->setConfig('newslibrary');
        $date = "{$this->year}-{$this->month}-{$this->day}";
        $cacheID = $this->view->generateCacheId($date, '', $this->page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))) {

            $fp = new \Frontpage();
            $cm = new \ContentManager();
            $allCategories = $this->ccm->categories;

            $library  = array();
            $contents = $cm->getContentsForLibrary($date);

            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $categoryID = $content->category;
                    if (!isset($library[$categoryID])) {
                        $library[$categoryID] = new \stdClass();
                    }
                    $library[$categoryID]->id         = $categoryID;
                    $library[$categoryID]->title      = $allCategories[$categoryID]->title;
                    $library[$categoryID]->contents[] = $content;
                }
            }

            $this->view->assign('library', $library);
        }

        // $this->getAds();

        return $this->render(
            'archive/archive.tpl',
            array(
                'cache_id' => $cacheID,
                'newslibraryDate' => $date,
                'actual_category' => 'archive',
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

        if (empty($this->categoryName) || ($this->categoryName == 'home')) {
            $categoryID = 0;
            $this->categoryName = 'home';
        } else {
            $categoryID = $this->ccm->get_id($this->categoryName);
        }

        $date = "{$this->year}-{$this->month}-{$this->day}";
        $this->view->setConfig('newslibrary');
        $cacheID = $this->view->generateCacheId($date.'_'.$this->categoryName, '', $this->page);

        if (($this->view->caching == 0)
           || (!$this->view->isCached('archive/archive.tpl', $cacheID))) {

             $cm = new \ContentManager();

            $library  = array();
            $library[$categoryID] = new \stdClass();
            $contents = $cm->getContentsForLibrary($date, $categoryID);

            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $library[$categoryID]->id         = $categoryID;
                    $library[$categoryID]->title      = $this->categoryName;
                    $library[$categoryID]->contents[] = $content;
                }
            }

            $this->view->assign('library', $library);
        }

        // $this->getAds();

        return $this->render(
            'archive/archive.tpl',
            array(
                'cache_id' => $cacheID,
                'category_name'   => $this->categoryName,
                'actual_category' => 'archive',
                'newslibraryDate' => $date,
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

        $path = "{$this->year}/{$this->month}/{$this->day}";
        $html = '';
        $file = MEDIA_PATH."/library/{$path}/{$this->categoryName}.html";
        if (file_exists($file) && is_readable($file)) {
            $html = file_get_contents(SITE_URL.INSTANCE_MEDIA."library/{$path}/{$this->categoryName}.html");
        } else {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        if (empty($html)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        return new Response($html);
    }
}
