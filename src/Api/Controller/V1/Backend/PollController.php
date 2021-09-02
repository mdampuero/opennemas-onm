<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PollController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'POLL_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'POLL_CREATE',
        'delete' => 'POLL_DELETE',
        'patch'  => 'POLL_UPDATE',
        'update' => 'POLL_UPDATE',
        'list'   => 'POLL_ADMIN',
        'save'   => 'POLL_CREATE',
        'show'   => 'POLL_UPDATE',
    ];

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_poll_get_item';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        $categories = $this->get('api.service.category')->responsify(
            $this->get('api.service.category')->getList()['items']
        );

        return array_merge(parent::getExtraData($items), [
            'authors'     => $this->getAuthors($items),
            'categories'  => $categories,
            'tags'        => $this->getTags($items),
            'total_votes' => $this->container->get('core.helper.poll')->getTotalVotes($items),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('Poll');
    }

    /**
     * {@inheritDoc}
     */
    public function saveItemAction(Request $request)
    {
        if (!is_array($request->request->all()['items']) || count($request->request->all()['items']) < 2) {
            $msg = $this->get('core.messenger');

            $msg->add('A minimum of 2 answers are required', 'error', 400);

            $response = new JsonResponse($msg->getMessages(), $msg->getCode());

            return $response;
        }

        return parent::saveItemAction($request);
    }

    /**
     * {@inheritDoc}
     */
    public function updateItemAction(Request $request, $id)
    {
        if (!is_array($request->request->all()['items']) || count($request->request->all()['items']) < 2) {
            $msg = $this->get('core.messenger');

            $msg->add(_('A minimum of 2 answers are required'), 'error', 400);

            $response = new JsonResponse($msg->getMessages(), $msg->getCode());

            return $response;
        }

        return parent::updateItemAction($request, $id);
    }
}
