<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OpinionController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'OPINION_CREATE',
        'update' => 'OPINION_UPDATE',
        'list'   => 'OPINION_ADMIN',
        'show'   => 'OPINION_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'opinion_inner'
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'opinion';

    /**
     * Lists the available opinions for the frontpage manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Opinion');

        $filters = [
            'content_type_name' => [ [ 'value' => 'opinion' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $opinions = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page);
        $total    = $em->countBy($filters);

        $this->get('core.locale')->setContext('frontend');

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $total,
            'route'       => [
                'name'   => 'backend_opinions_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render('opinion/content-provider.tpl', [
            'opinions'   => $opinions,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Handles the configuration for the opinion manager.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_SETTINGS')")
     */
    public function configAction()
    {
        return $this->render('opinion/config.tpl');
    }

    /**
     * Returns the list of authors.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        $response = $this->get('api.service.author')
            ->getList('order by name asc');

        return $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();
    }

    /**
     * This method load from the request the metadata fields,
     *
     * @param mixed   $data Data where load the metadata fields.
     * @param Request $postReq Request where the metadata are.
     * @param string  $type type of the extra field
     *
     * @return array
     */
    protected function loadMetaDataFields($data, $postReq, $type)
    {
        if (!$this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            return $data;
        }

        // If I don't have the extension, I don't check the settings
        $groups = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get($type);

        if (!is_array($groups)) {
            return $data;
        }

        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                if ($postReq->get($field['key'], null) == null) {
                    continue;
                }

                $data[$field['key']] = $postReq->get($field['key']);
            }
        }
        return $data;
    }
}
