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

        $category     = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "widget" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'widget');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        try {
            $oql .= 'order by created desc limit ' . $itemsPerPage;

            if ($page > 1) {
                $oql .= ' offset ' . ($page - 1) * $itemsPerPage;
            }

            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.widget')->getList($oql);

            $this->get('core.locale')->setContext($context);

            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => 8,
                'maxLinks'    => 5,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'backend_widgets_content_provider',
                    'params' => [
                        'category'             => $category,
                        'frontpage_version_id' => $version
                    ]
                ],
            ]);

            return $this->render('widget/content-provider.tpl', [
                'widgets'    => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }

    /**
     * Displays the form to quick edit an item.
     *
     * @param Request $request The request object.
     * @param integer $id      The item id.
     *
     * @return Response The response object.
     */
    public function quickShowAction(Request $request, $id)
    {
        $params = [ 'id' => $id ];

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/item-quick.tpl', $params);
    }
}
