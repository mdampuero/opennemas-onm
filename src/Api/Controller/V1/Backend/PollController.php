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
    protected $service = 'api.service.poll';

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
    public function getListAction(Request $request)
    {
        $format = $request->query->get('format', '');

        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $us  = $this->get($this->service);
        $oql = $request->query->get('oql', '');

        $response = $us->getList($oql);

        $items = $response['items'];
        if ($format == '.csv') {
            $rows = [
                "pk_content",
                "pretitle",
                "title",
                "description",
                "created",
                "changed",
                "starttime",
                "content_status",
                "body"
            ];

            $items = array_map(function ($a) use ($rows) {
                $item = [];

                foreach ($rows as $key) {
                    $item[$key] = $a->{$key};
                }

                $total_votes = $this->get('core.helper.poll')->getTotalVotes($a);

                $item['total_votes'] = $total_votes[$item['pk_content']];

                return $item;
            }, $response['items']);
        }

        return [
            'items'      => $us->responsify($items),
            'total'      => $response['total'],
            'extra'      => $this->getExtraData($response['items']),
            'o-filename' => $this->filename,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function saveItemAction(Request $request)
    {
        $response = $this->checkItems($request->request->all()['items']);

        return is_null($response) ? parent::saveItemAction($request) : $response;
    }

    /**
     * {@inheritDoc}
     */
    public function updateItemAction(Request $request, $id)
    {
        $response = $this->checkItems($request->request->all()['items']);

        return is_null($response) ? parent::updateItemAction($request, $id) : $response;
    }

    /**
     * Returns a response if the number of items fails
     *
     * @param array $items The list of items.
     *
     * @return response Response in case of error
     */
    private function checkItems($items)
    {
        if (!is_array($items) || count($items) < 2) {
            $msg = $this->get('core.messenger');

            $msg->add(_('A minimum of 2 answers are required'), 'error', 400);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        return null;
    }
}
