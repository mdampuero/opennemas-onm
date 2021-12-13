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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'WIDGET_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_widget_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'WIDGET_CREATE',
        'delete' => 'WIDGET_DELETE',
        'patch'  => 'WIDGET_UPDATE',
        'update' => 'WIDGET_UPDATE',
        'list'   => 'WIDGET_ADMIN',
        'save'   => 'WIDGET_CREATE',
        'show'   => 'WIDGET_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.widget';

    /**
     * Returns the parameters form for widgets of the given uuid.
     *
     * @param string $uuid The widget uuid.
     *
     * @return Response The response object.
     */
    public function getFormAction($uuid)
    {
        $this->get('core.loader.widget')->loadWidget($uuid);

        $widget = 'Widget' . $uuid;
        if (!class_exists($widget)) {
            return new Response('', 400);
        }

        $widget = new $widget(null);

        if (empty($widget->getForm())) {
            return new Response('', 400);
        }

        return new Response($widget->getForm());
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items),
            'classes'    => $this->getTypes()
        ]);
    }

    /**
     * Returns the list of widget types.
     *
     * @return array The list of widget types.
     */
    protected function getTypes()
    {
        $widgets = $this->get('core.loader.widget')->getWidgets();
        $types   = [];

        foreach ($widgets as $name) {
            $types[] = [ 'id' => $name, 'name' => $name ];
        }

        return $types;
    }
}
