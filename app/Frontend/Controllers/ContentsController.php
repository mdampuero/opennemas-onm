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

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class ContentsController extends Controller
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
     * Description of the action
     *
     * @return Response the response object
     **/
    public function printAction(Request $request)
    {
        $dirtyID      = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Resolve article ID
        $contentID        = \Content::resolveID($dirtyID);
        $cacheID = $this->view->generateCacheId('article', null, $contentID);

        // if (!$this->view->isCached('article/article_printer.tpl', $cacheID)) {
            $article = new \Article($contentID);

            // Foto interior
            if (isset($article->img2) and ($article->img2 != 0)) {
                $photoInt = new \Photo($article->img2);
                $this->view->assign('photoInt', $photoInt);
            }

            $this->view->assign('article', $article);
        // }


        return $this->render(
            'article/article_printer.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }



} // END class ContentsController