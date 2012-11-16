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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class PollsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->cm  = new \ContentManager();

        $action = $this->request->query->filter('action', 'frontpage', FILTER_SANITIZE_STRING);

        $this->categoryName = $this->get('request')->query->filter('category_name', '', FILTER_SANITIZE_STRING);

        if (!empty($this->categoryName)) {
            $this->ccm = new \ContentCategoryManager();
            $this->category     = $this->ccm->get_id($this->categoryName);
            $actual_category_id = $this->category; // FOR WIDGETS
            $category_real_name = $this->ccm->get_title($this->categoryName); //used in title

        } else {
            $category_real_name = 'Portada';
            $this->categoryName = 'home';
            $this->category     = 0;
            $actual_category_id = 0;
        }

        $this->view->assign(
            array(
                'category_name'         => $this->categoryName,
                'category'              => $this->category,
                'actual_category_id'    => $actual_category_id,
                'category_real_name'    => $category_real_name,
                'actual_category_title' => $category_real_name,
                'actual_category'       => $this->categoryName,
            )
        );

        $pollSettings = s::get('poll_settings');

        $this->view->assign(
            array('settings' => $pollSettings)
        );
    }

    /**
     * Renders the album frontpage
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        $this->page = $request->query->getDigits('page', 1);

        $this->view->setConfig('poll-frontpage');

        $cacheID = $this->view->generateCacheId('poll'.$this->categoryName, '', $this->page);

        /**
         * Don't execute action logic if was cached before
         */
        if ( ($this->view->caching == 0)
           || (!$this->view->isCached('poll/poll-frontpage.tpl', $cacheID))) {

            if (isset($this->category) && !empty($this->category)) {
                $polls = $this->cm->find_by_category(
                    'Poll',
                    $this->category,
                    'available=1',
                    'ORDER BY starttime DESC LIMIT 2'
                );

                $otherPolls = $this->cm->find(
                    'Poll',
                    'available=1',
                    'ORDER BY starttime DESC LIMIT 5'
                );
            } else {
                $polls = $this->cm->find(
                    'Poll',
                    'available=1 and in_home=1',
                    'ORDER BY starttime DESC LIMIT 2'
                );
                $otherPolls = $this->cm->find(
                    'Poll',
                    'available=1',
                    'ORDER BY starttime DESC LIMIT 2,7'
                );
            }

            if (!empty($polls)) {
                foreach ($polls as &$poll) {
                    $poll->items   = $poll->get_items($poll->id);
                    $poll->dirtyId = date('YmdHis', strtotime($poll->created)).sprintf('%06d', $poll->id);
                }
            }

            $this->view->assign(
                array(
                    'polls'      => $polls,
                    'otherPolls' => $otherPolls
                )
            );
        }

        //  require_once APP_PATH.'/../public/controllers/poll_advertisement.php';
        $this->pollAdvertisement('frontpage');

        return $this->render(
            'poll/poll_frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );


    }

    /**
     * Shows an inner poll
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->page = $request->query->getDigits('page', 1);

        $this->view->setConfig('poll-inner');

        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        $pollId = \Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($pollId)) {
            return new RedirectResponse($this->generateUrl('frontend_poll_frontpage'));
        }

        $poll = new \Poll($pollId);

        if (empty($poll->id)) {
            return new RedirectResponse($this->generateUrl('frontend_poll_frontpage'));
        }

        $cacheID = $this->view->generateCacheId($this->categoryName, '', $pollId);

        if ($this->view->caching == 0
            || !$this->view->isCached('poll/poll.tpl', $cacheID)
        ) {
            if (($poll->available==1) && ($poll->in_litter==0)) {

                $poll->items   = $poll->get_items($pollId);
                $items         = $poll->items;
                $poll->dirtyId = $dirtyID;

                $comment  = new \Comment();
                $comments = $comment->get_public_comments($pollId);

                $otherPolls = $this->cm->find(
                    'Poll',
                    'available=1 ',
                    'ORDER BY created DESC LIMIT 5'
                );

                $this->view->assign(
                    array(
                        'contentId'    => $pollId,
                        'poll'         => $poll,
                        'items'        => $items,
                        'num_comments' => count($comments),
                        'otherPolls'   => $otherPolls,
                    )
                );
            } // end if $tpl->is_cached


            // Used on module_comments.tpl
            $this->view->assign('contentId', $pollId);

        }

        $cookie = "polls".$pollId;
        $msg    = '';
        if (isset($_COOKIE[$cookie])) {
            if ($_COOKIE[$cookie]=='tks') {
                $msg = 'Ya ha votado esta encuesta';
            } else {
                $msg = 'Gracias, por su voto';
                //Application::setCookieSecure($cookie, 'tks');
                setcookie($cookie, 'tks', time()+3600);
            }
        }

        $this->view->assign('msg', $msg);

        $this->pollAdvertisement('inner');

        return $this->render(
            'poll/poll.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Add vote & show poll result
     *
     * @return Response the response object
     **/
    public function addVoteAction(Request $request)
    {

        $dirtyID = $request->request->filter('id', '', FILTER_SANITIZE_STRING);

        $pollId = Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($pollId)) {
            return new RedirectResponse($this->generateUrl('frontend_poll_frontpage'));
        }

        $poll = new Poll($pollId);

        if (!empty($poll->id)) {
            $cookie = "polls".$pollId;
            if (isset($_COOKIE[$cookie])) {
                 //Application::setCookieSecure($cookie, 'tks');
                setcookie($cookie, 'tks', time()+3600);
            }
            $respEncuesta = $request->request->filter('respEncuesta', '', FILTER_SANITIZE_STRING);

            if (!empty($respEncuesta) && !isset($_COOKIE[$cookie])) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $poll->vote($respEncuesta, $ip);
            }

            $tplManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
            $cacheID    = $this->view->generateCacheId($this->categoryName, '', $pollId);
            $tplManager->delete($cacheID, 'poll.tpl');

            $cacheID = $this->view->generateCacheId('poll'.$this->categoryName, '', $this->page);
            $tplManager->delete($cacheID, 'poll_frontpage.tpl');

            return new RedirectResponse(SITE_URL.$poll->uri);
        }
    }


    protected function pollAdvertisement($context = 'frontpage')
    {
        $advertisement = \Advertisement::getInstance();

        // Load internal banners, principal banners (1,2,3,11,13) and use cache to performance
        if ($context == 'inner') {
            $positions = array(901, 902, 903, 905, 909, 910);
            $intersticialId = 950;
        } else {
            $positions = array(801, 802, 803, 805, 809, 810);
            $intersticialId = 850;
        }
        $banners = $advertisement->getAdvertisements($positions, $this->category);

        $banners = $this->cm->getInTime($banners);
        //$advertisement->renderMultiple($banners, &$tpl);
        $advertisement->renderMultiple($banners, $advertisement);

        $intersticial = $advertisement->getIntersticial($intersticialId, $this->category);
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }

        return new Response('ok');
    }
}
// END class PollsController

