<?php
/**
 * Handles the actions for advertisements
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
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
        $this->cm   = new \ContentManager();

        $this->categoryName = $this->get('request')->query->filter('category_name', '', FILTER_SANITIZE_STRING);
        $this->page         = $this->get('request')->query->getDigits('page', 1);

        if (!empty($this->categoryName)) {
            $this->ccm = new \ContentCategoryManager();
            $this->category     = $this->ccm->get_id($this->categoryName);
            $actual_category_id = $this->category; // FOR WIDGETS
            $category_real_name = $this->ccm->getTitle($this->categoryName); //used in title
        } else {
            $category_real_name = 'Portada';
            $this->categoryName = 'home';
            $this->category     = 0;
            $actual_category_id = 0;
        }

        $pollSettings = s::get('poll_settings');

        $this->view->assign(
            array(
                'category_name'         => $this->categoryName,
                'category'              => $this->category,
                'actual_category_id'    => $actual_category_id,
                'category_real_name'    => $category_real_name,
                'actual_category_title' => $category_real_name,
                'actual_category'       => $this->categoryName,
                'settings'              => $pollSettings
            )
        );
    }

    /**
     * Renders the album frontpage
     *
     * @return Response the response object
     **/
    public function frontpageAction()
    {
        if (!\Onm\Module\ModuleManager::isActivated('POLL_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('poll-frontpage');

        // Don't execute action logic if was cached before
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $this->page);
        if (($this->view->caching == 0)
            || (!$this->view->isCached('poll/poll_frontpage.tpl', $cacheID))
        ) {
            if (isset($this->category) && !empty($this->category)) {
                $polls = $this->cm->find_by_category(
                    'Poll',
                    $this->category,
                    'content_status=1',
                    'ORDER BY starttime DESC LIMIT 2'
                );

                $otherPolls = $this->cm->find(
                    'Poll',
                    'content_status=1',
                    'ORDER BY starttime DESC LIMIT 5'
                );
            } else {
                $polls = $this->cm->find(
                    'Poll',
                    'content_status=1 and in_home=1',
                    'ORDER BY starttime DESC LIMIT 2'
                );
                $otherPolls = $this->cm->find(
                    'Poll',
                    'content_status=1',
                    'ORDER BY starttime DESC LIMIT 2,7'
                );
            }

            if (!empty($polls)) {
                foreach ($polls as &$poll) {
                    $poll->items   = $poll->getItems($poll->id);
                    $poll->dirtyId = date('YmdHis', strtotime($poll->created)).sprintf('%06d', $poll->id);
                    $poll->status  = 'opened';
                    if (is_string($poll->params)) {
                        $poll->params = unserialize($poll->params);
                    }
                    if (is_array($poll->params) && array_key_exists('closetime', $poll->params)
                        && (!empty($poll->params['closetime']))
                        && ($poll->params['closetime'] != date('00-00-00 00:00:00'))
                        && ($poll->params['closetime'] < date('Y-m-d H:i:s'))) {
                            $poll->status = 'closed';
                    }
                }
            }

            $this->view->assign(
                array(
                    'polls'      => $polls,
                    'otherPolls' => $otherPolls
                )
            );
        }

        $ads = $this->getAds('frontpage');
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'poll/poll_frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Shows a poll given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $this->page = $request->query->getDigits('page', 1);
        $dirtyID    = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        $urlSlug    = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $poll = $this->get('content_url_matcher')
            ->matchContentUrl('poll', $dirtyID, $urlSlug, $this->categoryName);

        if (empty($poll)) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('poll-inner');
        $cacheID = $this->view->generateCacheId($this->categoryName, '', $poll->id);
        if ($this->view->caching == 0
            || !$this->view->isCached('poll/poll.tpl', $cacheID)
        ) {
            $items         = $poll->items;
            $poll->dirtyId = $dirtyID;

            $otherPolls = $this->cm->find(
                'Poll',
                'content_status=1 ',
                'ORDER BY created DESC LIMIT 5'
            );

            $this->view->assign([
                'poll'       => $poll,
                'content'    => $poll,
                'contentId'  => $poll->id,
                'items'      => $items,
                'otherPolls' => $otherPolls,
            ]);

            // Used on module_comments.tpl
            $this->view->assign('contentId', $poll->id);
        }

        $cookieName = "poll-".$poll->id;
        $cookie = $request->cookies->get($cookieName);

        $message = null;
        $alreadyVoted = false;
        if ($poll->status != 'closed') {
            $voted = (int) $request->query->getDigits('voted', 0);
            $valid = (int) $request->query->getDigits('valid', 3);
            if ($voted == 1) {
                if ($voted == 1 && $valid === 1) {
                    $message = "<span class='thanks'>"._('Thanks for participating.')."</span>";
                } elseif ($voted == 1 && $valid === 0) {
                    $message = "<span class='wrong'>"._('Please select a valid poll answer.')."</span>";
                }
            } elseif (isset($cookie)) {
                $alreadyVoted = true;
                $message = "<span class='ok'>"._('You have voted this poll previously.')."</span>";
            } elseif (($valid === 0) && ($voted == 0)) {
                $alreadyVoted = true;
                $message = "<span class='ok'>"._('You have voted this poll previously.')."</span>";
            }
        } else {
            $message = "<span class='closed'>"._('You can\'t vote this poll, it is closed.')."</span>";
        }

        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'poll/poll.tpl',
            array(
                'cache_id'      => $cacheID,
                'msg'           => $message,
                'poll'          => $poll,
                'already_voted' => $alreadyVoted,
            )
        );
    }

    /**
     * Add vote & show poll result
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function addVoteAction(Request $request)
    {
        $answer  = $request->request->filter('answer', '', FILTER_SANITIZE_STRING);
        $pollID = $request->request->filter('id', '', FILTER_SANITIZE_STRING);

        $poll = $this->get('entity_repository')->find('Poll', $pollID);
        if (is_null($poll)) {
            throw new ResourceNotFoundException();
        }

        $cookieName = "poll-".$pollID;
        $cookie = $request->cookies->get($cookieName);

        $valid = 0;
        $voted = 0;

        if (!empty($answer) && !isset($cookie) && ($poll->status != 'closed')) {
            $ip    = getUserRealIP();
            $voted = $poll->vote($answer, $ip);

            $valid = 1;

            $cookieVoted = new Cookie($cookieName, 'voted', time() + (3600 * 1));

            // Clear all caches
            $this->cleanCache($poll->category_name, $pollID);
            $this->cleanCache('home', $pollID);
            dispatchEventWithParams('content.update', array('content' => $poll));
        } elseif (empty($answer)) {
            $valid = 0;
            $voted = 1;
        }

        $response = new RedirectResponse(SITE_URL.$poll->uri.'?voted='.$voted.'&valid='.$valid);
        if (isset($cookieVoted)) {
            $response->headers->setCookie($cookieVoted);
        }

        return $response;
    }

    /**
     * Fetches the ads given a context
     *
     * @param string $context the context to fetch ads from
     *
     * @return void
     **/
    protected function getAds($context = 'frontpage')
    {
        // Get polls positions
        $positionManager = getService('core.theme')->getAdsPositionManager();
        if ($context == 'inner') {
            $positions = $positionManager->getAdsPositionsForGroup('polls_inner', array(7, 9));
        } else {
            $positions = $positionManager->getAdsPositionsForGroup('polls_frontpage', array(7, 9));
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, $this->category);
    }

    /**
     * Clean the cache for a given poll
     *
     * @param string $categoryName the category where the clean has to be done
     * @param int $pollID the poll id where the clean has to be done
     *
     * @return void
     **/
    protected function cleanCache($categoryName, $pollID)
    {

        // TODO: remove cache cleaning actions
        $cacheManager = $this->get('template_cache_manager');
        $cacheManager->setSmarty(new \Template(TEMPLATE_USER_PATH));

        $cacheID      = $this->view->generateCacheId($categoryName, '', $pollID);
        $cacheManager->delete($cacheID, 'poll.tpl');

        $cacheID      = $this->view->generateCacheId('poll'.$categoryName, '', $this->page);
        $cacheManager->delete($cacheID, 'poll_frontpage.tpl');

        $cacheID      = $this->view->generateCacheId('poll'.$this->categoryName, '', $this->page);
        $cacheManager->delete($cacheID, 'poll_frontpage.tpl');
    }
}
