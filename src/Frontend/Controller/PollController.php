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

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays a poll or a list of polls.
 */
class PollController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'list'    => 'poll-frontpage',
        'show'    => 'poll-inner',
        'showamp' => 'poll-inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'POLL_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'list'    => 'polls_frontpage',
        'show'    => 'polls_inner',
        'showamp' => 'amp_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'polls_frontpage' => [ 7, 9 ],
        'polls_inner'     => [ 7 ],
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list'    => [ 'page', 'category_slug' ],
        'showamp' => [ '_format' ],
    ];

    /**
     * The list of routes per action.
     *
     * @var array
     */
    protected $routes = [
        'list' => 'frontend_poll_frontpage'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.poll';

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'list'    => 'poll/poll_frontpage.tpl',
        'show'    => 'poll/poll.tpl',
        'showamp' => 'amp/content.tpl',
    ];

    /**
     * Add vote & show poll result.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function voteAction(Request $request)
    {
        $answer   = (int) $request->request->get('answer');
        $poll     = $this->getItem($request);
        $response = $request->request->get('g-recaptcha-response');

        // Check reCAPTCHA
        $isValid = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->isValid($response, $request->getClientIp());

        if (!$isValid) {
            $this->get('session')->getFlashBag()
                ->add('error', _("The reCAPTCHA wasn't entered correctly."
                    . " Go back and try it again."));

            $this->get('core.dispatcher')
                ->dispatch('poll.vote', [ 'item' => $poll ]);

            return new RedirectResponse(
                $this->get('core.helper.url_generator')->generate($poll)
            );
        }

        // Prevent vote when no answer
        if (!$request->request->has('answer')) {
            $this->get('session')->getFlashBag()
                ->add('error', _('Error: no vote value!'));

            $this->get('core.dispatcher')
                ->dispatch('poll.vote', [ 'item' => $poll ]);

            return new RedirectResponse(
                $this->get('core.helper.url_generator')->generate($poll)
            );
        }

        // Prevent vote when poll is closed
        if ($this->get('core.helper.poll')->isClosed($poll)) {
            $this->get('session')->getFlashBag()
                ->add('error', _('You can\'t vote this poll, it is closed.'));

            $this->get('core.dispatcher')
                ->dispatch('poll.vote', [ 'item' => $poll ]);

            return new RedirectResponse(
                $this->get('core.helper.url_generator')->generate($poll)
            );
        }

        $cookieName = 'poll-' . $poll->pk_content;
        $cookie     = $request->cookies->get($cookieName);

        // Prevent vote when already voted
        if (!empty($cookie)) {
            $this->get('session')->getFlashBag()
                ->add('error', _('You have voted this poll previously.'));

            $this->get('core.dispatcher')
                ->dispatch('poll.vote', [ 'item' => $poll ]);

            return new RedirectResponse(
                $this->get('core.helper.url_generator')->generate($poll)
            );
        }

        try {
            $items = array_map(function ($item) use ($answer) {
                if ($item['pk_item'] == $answer) {
                    $item['votes']++;
                }
                return $item;
            }, $poll->items);

            //Unset percent attribute from items
            $items = array_map(function ($a) {
                unset($a['percent']);
                return $a;
            }, $items);

            $this->get($this->service)->updateItem($poll->pk_content, ['items' => $items]);

            $this->get('session')->getFlashBag()
                ->add('success', _('Thanks for participating.'));

            $cookie   = new Cookie($cookieName, 'voted');
            $response = new RedirectResponse(
                $this->get('core.helper.url_generator')->generate($poll)
            );

            $response->headers->setCookie($cookie);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()
                ->add('error', _('Error while updating content'));
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($params, $item = null)
    {
        $params = parent::getParameters($params, $item);

        $params['recaptcha'] = $this->get('core.recaptcha')
            ->configureFromSettings()
            ->getHtml();

        return $params;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoute($action, $params = [])
    {
        if ($action == 'list' && array_key_exists('category_slug', $params)) {
            return 'frontend_poll_frontpage_category';
        }

        return parent::getRoute($action, $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateList(array &$params = []) : void
    {
        $category = $params['o_category'];
        $date     = date('Y-m-d H:i:s');

        // Invalid page provided as parameter
        if ($params['page'] <= 0
            || $params['page'] > $this->getParameter('core.max_page')
        ) {
            throw new ResourceNotFoundException();
        }

        $epp = (int) $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $categoryOQL = !empty($category)
            ? sprintf(' and category_id=%d', $category->id)
            : '';

        $response = $this->get($this->service)->getList(sprintf(
            'content_type_name="poll" and content_status=1 and in_litter=0 %s '
            . 'and (starttime is null or starttime < "%s") '
            . 'and (endtime is null or endtime > "%s") '
            . 'order by starttime desc limit %d offset %d',
            $categoryOQL,
            $date,
            $date,
            $epp,
            $epp * ($params['page'] - 1)
        ));

        // No first page and no contents
        if ($params['page'] > 1 && empty($response['items'])) {
            throw new ResourceNotFoundException();
        }

        $total_votes = $this->get('core.helper.poll')->getTotalVotes($response['items']);

        $polls = [];
        $polls = array_map(function ($poll) use ($total_votes) {
            $items = array_map(function ($item) use ($poll, $total_votes) {
                $percent = round($item['votes'] /
                    ($total_votes[$poll->pk_content] > 0 ? $total_votes[$poll->pk_content] : 1), 4) * 100;

                $item['percent'] = sprintf('%.2f', $percent);

                return $item;
            }, $poll->items);
            $poll->items = $items;
            return $poll;
        }, $response['items']);

        $params = array_merge($params, [
            'polls'      => $polls,
            'total'      => $response['total'],
            'pagination' => $this->get('paginator')->get([
                'boundary'    => false,
                'directional' => true,
                'maxLinks'    => 0,
                'epp'         => $epp,
                'page'        => $params['page'],
                'total'       => $response['total'],
                'route'       => [
                    'name'   => empty($category)
                        ? 'frontend_poll_frontpage'
                        : 'frontend_poll_frontpage_category',
                    'params' => empty($category)
                        ? []
                        : [ 'category_slug' => $category->name ],
                ]

            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getItem(Request $request)
    {
        try {
            $item = $this->get($this->service)
                ->getItem($this->getIdFromRequest($request));
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        if (empty($item) || !$this->get('core.helper.content')->isReadyForPublish($item)) {
            throw new ResourceNotFoundException();
        }

        $total_votes = $this->get('core.helper.poll')->getTotalVotes($item);

        $item->items = array_map(function ($a) use ($item, $total_votes) {
            $percent = round($a['votes'] /
                ($total_votes[$item->pk_content] > 0 ? $total_votes[$item->pk_content] : 1), 4) * 100;

            $a['percent'] = sprintf('%.2f', $percent);

            return $a;
        }, $item->items);

        return $item;
    }
}
