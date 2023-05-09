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
            return $this->getResponse(
                'error',
                _("The reCAPTCHA wasn't entered correctly. Go back and try it again."),
                $poll
            );
        }

        // Prevent vote when no answer
        if (!$request->request->has('answer')) {
            return $this->getResponse('error', _('Error: no vote value!'), $poll);
        }

        // Prevent vote when poll is closed
        if ($this->get('core.helper.poll')->isClosed($poll)) {
            return $this->getResponse('error', _('You can\'t vote this poll, it is closed.'), $poll);
        }

        $cookieName = 'poll-' . $poll->pk_content;
        $cookie     = $request->cookies->get($cookieName);

        // Prevent vote when already voted
        if (!empty($cookie)) {
            return $this->getResponse('error', _('You have voted this poll previously.'), $poll);
        }

        try {
            $items = array_map(function ($item) use ($answer) {
                if ($item['pk_item'] == $answer) {
                    $item['votes']++;
                }
                //Unset percent attribute from items
                unset($item['percent']);

                return $item;
            }, $poll->items);

            $this->get($this->service)->updateVotedItem($poll->pk_content, ['items' => $items]);

            return $this->getResponse('success', _('Thanks for participating.'), $poll);
        } catch (\Exception $e) {
            return $this->getResponse('error', _('Error while updating content'), $poll);
        }
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

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        if (!empty($expire)) {
            $this->setViewExpireDate($expire);

            $params['x-cache-for'] = $expire;
        }

        $params['x-tags'] .= ',poll-frontpage';

        if (!empty($category)) {
            $params['x-tags'] .= sprintf(',category-poll-%d', $category->id);
        }

        $params = array_merge($params, [
            'polls'      => $response['items'],
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
     * Returns the resposne with the passed parameters
     *
     * @param string $type The response type.
     * @param string $msg The response message.
     * @param object $poll The poll to generate url.
     *
     * @return Response The response object.
     */
    private function getResponse($type, $msg, $poll)
    {
        $this->get('session')->getFlashBag()
            ->add($type, $msg);

        $response = new RedirectResponse(
            $this->get('core.decorator.url')->prefixUrl($this->get('core.helper.url_generator')->generate($poll))
        );

        if ($type == 'success') {
            $cookie = new Cookie('poll-' . $poll->pk_content, '1', time() + (60 * 60 * 24 * 30 * 12));
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
