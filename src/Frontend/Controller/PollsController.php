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
use Onm\Message as m;
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
        $cacheID = $this->view->generateCacheId('poll'.$this->categoryName, '', $this->page);
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

        $this->view->setConfig('poll-inner');

        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        $pollId = \Content::resolveID($dirtyID);

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($pollId)) {
            throw new ResourceNotFoundException();
        }

        $poll = $this->get('entity_repository')->find('Poll', $pollId);

        if (empty($poll->id)) {
            throw new ResourceNotFoundException();
        }

        $cacheID = $this->view->generateCacheId($this->categoryName, '', $pollId);

        if ($this->view->caching == 0
            || !$this->view->isCached('poll/poll.tpl', $cacheID)
        ) {
            if ($poll->content_status == 1
                && $poll->in_litter == 0
            ) {
                $items         = $poll->items;
                $poll->dirtyId = $dirtyID;

                $otherPolls = $this->cm->find(
                    'Poll',
                    'content_status=1 ',
                    'ORDER BY created DESC LIMIT 5'
                );

                $this->view->assign(
                    array(
                        'poll'       => $poll,
                        'content'    => $poll,
                        'contentId'  => $pollId,
                        'items'      => $items,
                        'otherPolls' => $otherPolls,
                    )
                );
            } // end if $tpl->is_cached

            // Used on module_comments.tpl
            $this->view->assign('contentId', $pollId);
        }

        $cookieName = "poll-".$poll->id;
        $cookie = $request->cookies->get($cookieName);

        $message = null;
        $alreadyVoted = false;
        $voted = (int) $request->query->getDigits('voted', 0);
        $valid = (int) $request->query->getDigits('valid', 3);
        if ($voted == 1) {
            if ($voted == 1 && $valid === 1) {
                $message = _('Thanks for participating.');
            } elseif ($voted == 1 && $valid === 0) {
                $message = _('Please select a valid poll answer.');
            }
        } elseif (isset($cookie)) {
            $alreadyVoted = true;
            $message = _('You have voted this poll previously.');
        } elseif (($valid === 0) && ($voted == 0)) {
            $alreadyVoted = true;
            $message = _('You have voted this poll previously.');
        }

        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'poll/poll.tpl',
            array(
                'cache_id'      => $cacheID,
                'msg'           => $message,
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
        $dirtyID = $request->request->filter('id', '', FILTER_SANITIZE_STRING);
        $answer = $request->request->filter('answer', '', FILTER_SANITIZE_STRING);
        $pollId = \Content::resolveID($dirtyID);

        if (empty($pollId) || is_null($pollId)) {
            $pollId = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        }

        // Redirect to album frontpage if id_album wasn't provided
        if (is_null($pollId)) {
            throw new ResourceNotFoundException();
        }
        $poll = $this->get('entity_repository')->find('Poll', $pollId);

        if (empty($poll->id)) {
            throw new ResourceNotFoundException();
        }

        $cookieName = "poll-".$pollId;
        $cookie = $request->cookies->get($cookieName);

        $valid = 0;
        $voted = 0;

        if (!empty($answer) && !isset($cookie)) {
            $ip = getRealIp();
            $voted = $poll->vote($answer, $ip);

            $valid = 1;

            $cookieVoted = new Cookie($cookieName, 'voted', time() + (3600 * 1));

            // Clear all caches
            $this->cleanCache($poll->category_name, $pollId);
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
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
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
     * @param int $pollId the poll id where the clean has to be done
     *
     * @return void
     **/
    protected function cleanCache($categoryName, $pollId)
    {
        $tplManager = new \TemplateCacheManager($this->view->templateBaseDir);
        $cacheID    = $this->view->generateCacheId($categoryName, '', $pollId);
        $tplManager->delete($cacheID, 'poll.tpl');

        $cacheID = $this->view->generateCacheId('poll'.$categoryName, '', $this->page);
        $tplManager->delete($cacheID, 'poll_frontpage.tpl');

        $cacheID = $this->view->generateCacheId('poll'.$this->categoryName, '', $this->page);
        $tplManager->delete($cacheID, 'poll_frontpage.tpl');
    }
}
