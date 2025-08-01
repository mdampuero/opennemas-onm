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

use Api\Controller\V1\ApiController;
use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class NewsAgencyResourceController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'NEWS_AGENCY_IMPORTER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'import'      => 'IMPORT_ADMIN',
        'list'        => 'IMPORT_ADMIN',
        'show'        => 'IMPORT_ADMIN'
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.news_agency.resource';

    /**
     * Returns the content of the resource.
     *
     * @param string $id The resource id.
     *
     * @return Response The response object.
     */
    public function getContentAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('show'));

        $file = $this->get($this->service)->getContent($id);

        return new Response(
            $file->getContents(),
            200,
            [ 'content-type' => MimeTypeTool::getMimeType($file) ]
        );
    }

    /**
     * Creates a new content based on a news agency resource.
     *
     * @param Request $request The request object.
     * @param string  $id      The resource id.
     *
     * @return JsonResponse The response object.
     */
    public function importItemAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('import'));

        $params = $request->request->all();
        $msg    = $this->get('core.messenger');

        $id = $this->get($this->service)->importItem($id, $params);

        $msg->add(_('Item imported successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        if (empty($params['content_status'])) {
            $route = 'backend_' . $params['content_type_name'] . '_show';

            $response->headers->set('location', $this->generateUrl($route, [
                'id' => $id
            ]));
        }

        return $response;
    }

    /**
     * Creates contents based on a list of news agency resource.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function importListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('import'));

        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $imported = $this->get($this->service)->importList($ids, $params);

        if ($imported > 0) {
            $msg->add(
                sprintf(_('%s items imported successfully'), $imported),
                'success'
            );
        }

        if ($imported !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be imported successfully'),
                count($ids) - $imported
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of parameters for template.
     *
     * @return array The parameters for template.
     */
    protected function getExtraData($items = [])
    {
        $servers = $this->get('api.service.news_agency.server')->getList()['items'];
        $urns    = [];
        $related = [];

        foreach ($items as $item) {
            $urns[]  = $item->urn;
            $related = array_merge($related, $item->related);
        }

        $imported = $this->get('api.service.content')
            ->getList(sprintf('urn_source in ["%s"]', implode('","', $urns)));

        $imported = array_map(function ($a) {
            return $a->urn_source;
        }, $imported['items']);

        $related = $this->get($this->service)->getListByIds($related)['items'];

        $related = $this->get('data.manager.filter')
            ->set($related)
            ->filter('mapify', [ 'key' => 'id' ])
            ->get($related);

        $types = [
            [ 'name' => _('Text'), 'value' => 'text' ],
            [ 'name' => _('Photo'), 'value' => 'photo' ]
        ];

        return [
            'imported' => $imported,
            'related'  => $related,
            'servers'  => $servers,
            'types'    => $types
        ];
    }
}
