<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Common\Core\Controller\Controller;

/**
 * Displays a poll or a list of polls.
 */
class PollsController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $this->cm = new \ContentManager();

        $request            = $this->get('request_stack')->getCurrentRequest();
        $this->categoryName = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);

        if (!empty($this->categoryName)) {
            $this->ccm          = new \ContentCategoryManager();
            $this->category     = $this->ccm->get_id($this->categoryName);
            $actual_category_id = $this->category; // FOR WIDGETS
            $category_real_name = $this->ccm->getTitle($this->categoryName); //used in title
        } else {
            $category_real_name = 'Portada';
            $this->categoryName = 'home';
            $this->category     = 0;
            $actual_category_id = 0;
        }

        $this->view->assign([
            'category_name'         => $this->categoryName,
            'category'              => $this->category,
            'actual_category_id'    => $actual_category_id,
            'category_real_name'    => $category_real_name,
            'actual_category_title' => $category_real_name,
            'actual_category'       => $this->categoryName,
            'settings'              => $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('poll_settings'),
        ]);
    }

    /**
     * Renders the polls frontpage.
     *
     * @return Response The response object.
     */
    public function frontpageAction(Request $request)
    {
        if (!$this->get('core.security')->hasExtension('POLL_MANAGER')) {
            throw new ResourceNotFoundException();
        }

        $page     = $request->query->getDigits('page', 1);
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('poll_settings');
        $epp      = is_array($settings) && array_key_exists('epp', $settings)
            ? $settings['epp'] : 10;

        // Setup templating cache layer
        $this->view->setConfig('poll-frontpage');
        $cacheID = $this->view->getCacheId('frontpage', 'poll', $this->categoryName, $page);

        if (($this->view->getCaching() === 0)
            || (!$this->view->isCached('poll/poll_frontpage.tpl', $cacheID))
        ) {
            $filter = [
                'content_type_name' => [[ 'value' => 'poll' ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'starttime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'         => [
                    'union' => 'OR',
                    [ 'value' => '0000-00-00 00:00:00' ],
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            if (isset($this->category) && !empty($this->category)) {
                $filter['category_name'] = [[ 'value' => $this->categoryName ]];
            }

            $polls = $this->get('entity_repository')->findBy(
                $filter,
                ['starttime' => 'DESC'],
                $epp,
                $page
            );

            if (!empty($polls)) {
                foreach ($polls as &$poll) {
                    $poll->items   = $poll->getItems($poll->id);
                    $poll->dirtyId = date('YmdHis', strtotime($poll->created))
                        . sprintf('%06d', $poll->id);
                    $poll->status  = 'opened';
                    if (is_string($poll->params)) {
                        $poll->params = unserialize($poll->params);
                    }

                    if (is_array($poll->params)
                        && array_key_exists('closetime', $poll->params)
                        && (!empty($poll->params['closetime']))
                        && ($poll->params['closetime'] != '0000-00-00 00:00:00')
                        && ($poll->params['closetime'] < date('Y-m-d H:i:s'))
                    ) {
                            $poll->status = 'closed';
                    }
                }
            }

            $countPolls = $this->get('entity_repository')->countBy($filter);
            $pagination = $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $countPolls,
                'route'       => [
                    'name'   => 'frontend_poll_frontpage_category',
                    'params' => [
                        'category_name' => $this->categoryName,
                        'component'     => 'encuesta'
                    ]
                ]
            ]);

            $this->view->assign([
                'polls'      => $polls,
                'page'       => $page,
                'pagination' => $pagination
            ]);
        }

        list($positions, $advertisements) = $this->getAds($this->category, 'frontpage');

        return $this->render('poll/poll_frontpage.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $cacheID,
        ]);
    }

    /**
     * Shows a poll given its id.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $poll = $this->get('content_url_matcher')
            ->matchContentUrl('poll', $dirtyID, $urlSlug, $this->categoryName);

        if (empty($poll)) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('poll-inner');
        $cacheID = $this->view->getCacheId('content', $poll->id);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached('poll/poll.tpl', $cacheID)
        ) {
            $items         = $poll->items;
            $poll->dirtyId = $dirtyID;

            $otherPolls = $this->get('entity_repository')->findBy(
                [
                    'content_type_name' => [[ 'value' => 'poll']],
                    'content_status'    => [[ 'value' => 1 ]],
                    'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                    'starttime'         => [
                        'union' => 'OR',
                        [ 'value' => '0000-00-00 00:00:00' ],
                        [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                        [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                    ],
                    'endtime'         => [
                        'union' => 'OR',
                        [ 'value' => '0000-00-00 00:00:00' ],
                        [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                        [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                    ]
                ],
                ['starttime' => 'DESC'],
                5,
                1
            );

            $this->view->assign([
                'items'      => $items,
                'otherPolls' => $otherPolls,
            ]);
        }

        $cookieName = "poll-" . $poll->id;
        $cookie     = $request->cookies->get($cookieName);

        $message      = null;
        $alreadyVoted = false;
        if (is_array($poll->params) && array_key_exists('closetime', $poll->params)
            && (!empty($poll->params['closetime']))
            && ($poll->params['closetime'] != '0000-00-00 00:00:00')
            && ($poll->params['closetime'] < date('Y-m-d H:i:s'))
        ) {
            $poll->status = 'closed';
            $message      = "<span class='closed'>"
                . _('You can\'t vote this poll, it is closed.') . "</span>";
        } else {
            $voted = (int) $request->query->getDigits('voted', 0);
            $valid = (int) $request->query->getDigits('valid', 3);
            if ($voted == 1) {
                if ($voted == 1 && $valid === 1) {
                    $alreadyVoted = true;
                    $message      = "<span class='thanks'>"
                        . _('Thanks for participating.') . "</span>";
                } elseif ($voted == 1 && $valid === 0) {
                    $message = "<span class='wrong'>"
                        . _('Please select a valid poll answer.') . "</span>";
                }
            } elseif (isset($cookie)) {
                $alreadyVoted = true;
                $message      = "<span class='ok'>"
                    . _('You have voted this poll previously.') . "</span>";
            } elseif (($valid === 0) && ($voted == 0)) {
                $alreadyVoted = true;
                $message      = "<span class='ok'>"
                    . _('You have voted this poll previously.') . "</span>";
            }
        }

        list($positions, $advertisements) = $this->getAds($this->category, 'inner');

        return $this->render('poll/poll.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'poll'           => $poll,
            'content'        => $poll,
            'contentId'      => $poll->id,
            'cache_id'       => $cacheID,
            'msg'            => $message,
            'o_content'      => $poll,
            'already_voted'  => $alreadyVoted,
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($poll->tag_ids)['items']
        ]);
    }

    /**
     * Add vote & show poll result.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function addVoteAction(Request $request)
    {
        $answer = $request->request->filter('answer', '', FILTER_SANITIZE_STRING);
        $pollID = $request->request->filter('id', '', FILTER_SANITIZE_STRING);

        $poll = $this->get('entity_repository')->find('Poll', $pollID);
        if (is_null($poll)) {
            throw new ResourceNotFoundException();
        }

        $cookieName = "poll-" . $pollID;
        $cookie     = $request->cookies->get($cookieName);

        $valid = 0;
        $voted = 0;

        if (!empty($answer) && !isset($cookie) && ($poll->status != 'closed')) {
            $ip    = getUserRealIP();
            $voted = $poll->vote($answer, $ip);

            $valid = 1;

            $cookieVoted = new Cookie($cookieName, 'voted', time() + (3600 * 1));

            // Clear all caches
            dispatchEventWithParams('content.update', [ 'content' => $poll ]);
        } elseif (empty($answer)) {
            $valid = 0;
            $voted = 1;
        }

        $response = new RedirectResponse(
            '/' . $poll->uri . '?voted=' . $voted . '&valid=' . $valid
        );

        if (isset($cookieVoted)) {
            $response->headers->setCookie($cookieVoted);
        }

        return $response;
    }

    /**
     * Fetches the ads given a category and page.
     *
     * @param mixed $category The category id.
     * @param string $page    The page type.
     *
     * @return array The list of advertisements.
     */
    protected function getAds($category = 'home', $page = '')
    {
        $category = !isset($category) || ($category == 'home') ? 0 : $category;

        // Get polls positions
        $positionManager = $this->get('core.helper.advertisement');

        if ($page == 'inner') {
            $positions = $positionManager->getPositionsForGroup('polls_inner', [ 7 ]);
        } else {
            $positions = $positionManager->getPositionsForGroup('polls_frontpage', [ 7, 9 ]);
        }

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
