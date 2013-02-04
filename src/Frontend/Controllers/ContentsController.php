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


use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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

        // Resolve article ID
        $contentID = \Content::resolveID($dirtyID);
        $cacheID   = $this->view->generateCacheId('article', null, $contentID);

        $content = new \Content($contentID);
        $content = $content->get($contentID);

        if (isset($content->img2) && ($content->img2 != 0)) {
            $photoInt = new \Photo($content->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        return $this->render(
            'article/article_printer.tpl',
            array(
                'cache_id' => $cacheID,
                'content'  => $content,
                'article'  => $content
            )
        );
    }

    /**
     * Print an external article
     *
     * @return Response the response object
     **/
    public function extPrintAction(Request $request)
    {
        $dirtyID      = $request->query->filter('content_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        $cm = new \ContentManager;

        // Getting Synchronize setting params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$categoryName.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        // Resolve article ID
        $contentID = $cm->getUrlContent($wsUrl.'/ws/contents/resolve/'.$dirtyID, true);
        $cacheID   = $this->view->generateCacheId('article', null, $contentID);

        // Fetch content
        $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
        $content = unserialize($content);

        if (isset($content->img2) && ($content->img2 != 0)) {
            $photoInt = new \Photo($content->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        return $this->render(
            'article/article_printer.tpl',
            array(
                'cache_id' => $cacheID,
                'content'  => $content,
                'article'  => $content
            )
        );
    }

    /**
     * Shares an content by email
     *
     * @return Response the response object
     **/
    public function shareByEmailAction(Request $request)
    {
        $session = $this->get('session');
        if ('POST' == $request->getMethod()) {
            // Check direct access
            if ($session->get('sendformtoken') != $request->request->get('token')) {
                throw new ResourceNotFoundException();
            }

            // Send article to friend
            require_once SITE_VENDOR_PATH."/phpmailer/class.phpmailer.php";

            $tplMail = new \Template(TEMPLATE_USER);

            $mail = new \PHPMailer();

            $mail->Host     = "localhost";
            $mail->Mailer   = "smtp";

            $mail->CharSet = 'UTF-8';
            $mail->Priority = 5; // Low priority
            $mail->IsHTML(true);

            $mail->From     = $request->request->filter('sender', null, FILTER_SANITIZE_STRING);
            $mail->FromName = $request->request->filter('name_sender', null, FILTER_SANITIZE_STRING);
            $mail->Subject  =
                $request->request->filter('name_sender', null, FILTER_SANITIZE_STRING)
                .' ha compartido contigo un contenido de '.s::get('site_name');

            $tplMail->assign('destination', 'amig@,');

            $contentID = $request->request->getDigits('content_id', null);

            if ($ext == 1) {
                // External load content
                $content = $cm->getUrlContent($wsUrl.'/ws/contents/read/'.$contentID, true);
                $content = unserialize($content);
            } else {
                // Locally load content
                $content = new \Content($contentID);
            }

            $tplMail->assign('mail', $mail);
            $tplMail->assign('article', $content);

            // Filter tags before send
            $permalink = preg_replace('@([^:])//@', '\1/', SITE_URL . $content->uri);
            $message = $request->request->filter('body', null, FILTER_SANITIZE_STRING);
            $tplMail->assign('body', $message);
            if (!empty($content->agency)) {
                $agency = $content->agency;
            } else {
                $agency = s::get('site_name');
            }
            $tplMail->assign('agency', $agency);

            if (empty($content->summary)) {
                $summary = substr(strip_tags(stripslashes($content->body)), 0, 300)."...";
            } else {
                $summary = stripslashes($content->summary);
            }
            $tplMail->assign('summary', $summary);

            foreach ($this->view->plugins_dir as $value) {
                $filepath = $value ."/function.articledate.php";
                if (file_exists($filepath)) {
                    require_once $filepath;
                }
            }

            //require_once $tpl->_get_plugin_filepath('function', 'articledate');
            $params['created'] = $content->created;
            $params['updated'] = $content->updated;
            $params['article'] = $content;
            $date = smarty_function_articledate($params, $this->view);
            $tplMail->assign('date', $date);

            $tplMail->caching = 0;
            $mail->Body = $tplMail->fetch('email/send_to_friend.tpl');

            $mail->AltBody = $tplMail->fetch('email/send_to_friend_just_text.tpl');

            /**
             * Implementacion para enviar a multiples destinatarios
             * separados por coma
             */
            $destinatarios = explode(',', $_REQUEST['destination']);

            foreach ($destinatarios as $dest) {
                $mail->AddBCC(trim($dest));
            }

            if ( $mail->Send() ) {
                $this->view->assign('message', 'Noticia enviada correctamente.');
                $httpCode = 200;
            } else {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                    && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
                ) {
                    header("HTTP/1.0 404 Not Found");
                }
                $this->view->assign(
                    'message',
                    'La noticia no pudo ser enviada, inténtelo de nuevo más tarde.'
                    .'<br /> Disculpe las molestias.'
                );

                $httpCode = 500;
            }
            $content = $this->render('common/share_by_mail.tpl');

            return new Response($content, $httpCode);
        } else {
            $contentId = $request->query->getDigits('content_id', null);

            $session = $this->get('session');

            $token = md5(uniqid('sendform'));
            $session->set('sendformtoken', $token);

            $article = new \Content($contentId);

            return $this->render(
                'common/share_by_mail.tpl',
                array(
                    'content'    => $article,
                    'content_id' => $contentId,
                    'token'      => $token,
                )
            );
        }
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

            $content = new \Content($contentId);

            if (is_null($content->id)) {
                // Content does not exists so raise an Not Found exception
                throw new ResourceNotFoundException();
            } else {
                $rating = new \Rating($content->id);
                $update = $rating->update($voteValue, $ip);

                // Render the rating system after rating the content
                $content = $rating->render('', 'result', 1);

                // Return the content and set a cookie for avoiding multiple rates
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

            // Render the rating system
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

        // Raise exception if content id is not provided
        if ($contentId <= 0) {
            throw new ResourceNotFoundException();
        }

        // Increment view only if the request is performed with an AJAX request
        if ($request->isXmlHttpRequest()) {
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
