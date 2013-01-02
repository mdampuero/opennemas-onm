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
use Symfony\Component\HttpFoundation\Cookie;
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
        if (isset($article->img2) && ($article->img2 != 0)) {
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

    /**
     * Adds a vote for a content
     *
     * @return Response the response object
     **/
    public function rateContentAction(Request $request)
    {

        // If is POST request perform the vote action
        // if not render the vote
        if ('POST' == $request->getMethod()) {
            $ip        = $_SERVER['REMOTE_ADDR'];
            $contentId = $request->request->getDigits('content_id', null);
            $voteValue = $request->request->getDigits('vote_value', null);

            // Check if this content_id exists
            $content = new \Content($contentId);

            if (is_null($content->id)) {
                $content = _("Content not available");

                $response = new Response($content, 404);
            } else {
                $rating = new \Rating($content->id);
                $update = $rating->update($voteValue, $ip);

                $content = $rating->render('', 'result', 1);

                $response = new Response($content, 200);
                $response->headers->setCookie(
                    new Cookie(
                        "rating-" . $contentId,
                        'true',
                        time() + 60 * 60 * 24 * 30
                    )
                );
            }
        } else {
            $contentId = $request->query->getDigits('content_id', null);
            $alreadyVoted = ($request->cookies->get('rating-'.$contentId) !== null) ? 'result' : 'vote';

            // Render the rating
            $rating   = new \Rating($contentId);
            $content  = $rating->render('', $alreadyVoted);

            $response = new Response($content, 200);
        }

        return $response;
    }

    /**
     * Increments the num views for a content given its id
     *
     * @return Response the response object
     **/
    public function statsAction(Request $request)
    {
        $contentId = $request->query->getDigits('content_id', 0);

        if ($contentId <= 0) {
            $httpCode = 400;
            $content = 'Not content identifier provided.';
        }
        if ($request->isXmlHttpRequest() && $contentId > 0) {
            \Content::setNumViews($contentId);
            $httpCode = 200;
            $content = "Ok";
        } else {
            $httpCode = 400;
            $content = "Not AJAX request";
        }

        return new Response($content, $httpCode);
    }
}
