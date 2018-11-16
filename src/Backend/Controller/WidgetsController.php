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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class WidgetsController extends Controller
{
    /**
     * List widgets.
     *
     * @return Response the response object
     *
     * @Security("hasExtension('WIDGET_MANAGER')
     *     and hasPermission('WIDGET_ADMIN')")
     */
    public function listAction()
    {
        $types = $this->get('widget_repository')->getWidgets();
        $types = array_map(function ($a) {
            return [ 'name' => $a, 'value' => $a ];
        }, $types);

        array_unshift($types, [ 'name' => _('All'), 'value' => null ]);

        return $this->render('widget/list.tpl', [
            'all_widgets' => $types,
        ]);
    }

    /**
     * Show a selected Widget by id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('WIDGET_MANAGER')
     *     and hasPermission('WIDGET_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);
        // Need category to redirect to frontpage manager
        $category = $request->query->get('category', 'home');

        $widget = new \Widget($id);

        if (is_string($widget->params) && !empty($widget->params)) {
            $widget->params = unserialize($widget->params);
        }

        if (is_null($widget->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a widget with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_widgets'));
        }

        $allInteligentWidgets = $this->get('widget_repository')->getWidgets();

        return $this->render('widget/new.tpl', [
            'all_widgets'  => $allInteligentWidgets,
            'id'           => $id,
            'widget'       => $widget,
            'page'         => $page,
            'category'     => $category
        ]);
    }

    /**
     * Create a new widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('WIDGET_MANAGER')
     *     and hasPermission('WIDGET_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $post = $request->request;

            $title      = $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
            $tagIds     = $this->getTags($title);
            $widgetData = [
                'id'             => $post->getDigits('id'),
                'action'         => $post->filter('action', null, FILTER_SANITIZE_STRING),
                'title'          => $title,
                'tag_ids'        => $tagIds,
                'content_status' => (int) $post->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'renderlet'      => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
                'description'    => $post->get('description', ''),
                'content'        => $post->filter('content', ''),
                'params'         => json_decode($post->get('parsedParams', null)),
            ];

            if ($widgetData['renderlet'] == 'intelligentwidget') {
                $widgetData['content'] = $post->filter('intelligent_type', null, FILTER_SANITIZE_STRING);
            }

            if (count($widgetData['params']) > 0) {
                $newParams = [];
                foreach ($widgetData['params'] as $param) {
                    $newParams [$param->name] = $param->value;
                }

                $widgetData['params'] = $newParams;
            }

            try {
                $widget = new \Widget();
                $widget->create($widgetData);
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('error', $e->getMessage());

                return $this->redirect($this->generateUrl('admin_widget_create'));
            }

            $this->get('session')->getFlashBag()->add('success', _('Widget created successfully.'));

            return $this->redirect($this->generateUrl('admin_widget_show', ['id' => $widget->id]));
        } else {
            $allInteligentWidgets = $this->get('widget_repository')->getWidgets();

            return $this->render('widget/new.tpl', [
                'all_widgets' => $allInteligentWidgets,
                'action'      => 'new',
            ]);
        }
    }

    /**
     * Update an existing widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('WIDGET_MANAGER')
     *     and hasPermission('WIDGET_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);
        $post = $request->request;

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add('error', _("Widget data sent not valid."));

            return $this->redirect($this->generateUrl('admin_widget_show', [ 'id' => $id ]));
        }

        $title      = $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $tagIds     = $this->getTags($title);
        $widgetData = [
            'id'              => $id,
            'action'          => $post->filter('action', null, FILTER_SANITIZE_STRING),
            'title'           => $title,
            'tag_ids'         => $tagIds,
            'content_status'  => (int) $post->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'renderlet'       => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
            'description'     => $post->get('description', ''),
            'content'         => $post->filter('content', ''),
            'intelligentType' => $post->filter('intelligent_type', null, FILTER_SANITIZE_STRING),
            'params'          => json_decode($post->get('parsedParams', null)),
        ];

        if (count($widgetData['params']) > 0) {
            $newParams = [];
            foreach ($widgetData['params'] as $param) {
                $newParams[$param->name] = $param->value;
            }

            $widgetData['params'] = $newParams;
        }

        if ($widgetData['renderlet'] == 'intelligentwidget'
            && !empty($widgetData['intelligentType'])
        ) {
            $widgetData['content'] = $widgetData['intelligentType'];
        }

        $widget = new \Widget();
        if (!$widget->update($widgetData)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while updating the widget.')
            );

            return $this->redirect(
                $this->generateUrl('admin_widgets', [ 'page' => $page, ])
            );
        }

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Widget updated successfully.')
        );

        return $this->redirect(
            $this->generateUrl('admin_widget_show', [ 'id' => $id, ])
        );
    }

    /**
     * The content provider for widget
     *
     * @param  Request  $request the request object
     * @return Response          the response object
     *
     * @Security("hasExtension('WIDGET_MANAGER')")
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

    /**
     * Returns the list of tag ids basing on the advertisement title.
     *
     * @param string $title The advertisement title.
     *
     * @return array The list of tag ids.
     */
    protected function getTags($title)
    {
        $tags = $this->get('api.service.tag')->getListByString($title);

        return array_map(function ($a) {
            return $a->id;
        }, $tags['items']);
    }
}
