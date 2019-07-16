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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class WidgetController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'WIDGET_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'WIDGET_CREATE',
        'update' => 'WIDGET_UPDATE',
        'list'   => 'WIDGET_ADMIN',
        'show'   => 'WIDGET_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'widget';

    /**
     * Renders the content provider for widgets.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $this->checkSecurity($this->extension);

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
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Widget');

        $filters = [
            'content_type_name' => [ [ 'value' => 'widget' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $countWidgets = true;
        $widgets      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countWidgets);

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countWidgets,
            'route'       => [
                'name'   => 'admin_widgets_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render('widget/content-provider.tpl', [
            'widgets'    => $widgets,
            'pagination' => $pagination,
        ]);
    }
}
