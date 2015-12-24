<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class WidgetsController extends Controller
{
    /**
     * List widgets.
     *
     * @return Response the response object
     *
     * @Security("has_role('WIDGET_ADMIN')")
     *
     * @CheckModuleAccess(module="WIDGET_MANAGER")
     */
    public function listAction()
    {
        $allInteligentWidgets = \Widget::getAllInteligentWidgets();

        $allInteligentWidgetsContents = [];
        foreach ($allInteligentWidgets as $type) {
            $allInteligentWidgetsContents[] = [ 'name' => $type, 'value' => $type ];
        }

        array_unshift($allInteligentWidgetsContents, [ 'name' => _('All'), 'value' => -1 ]);

        return $this->render(
            'widget/list.tpl',
            [
                'all_widgets' => $allInteligentWidgetsContents,
            ]
        );
    }

    /**
     * Show a selected Widget by id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('WIDGET_UPDATE')")
     *
     * @CheckModuleAccess(module="WIDGET_MANAGER")
     */
    public function showAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);
        // Need category to redirect to frontpage manager
        $category = $request->query->get('category', 'home');

        $widget = new \Widget($id);
        $widgetParams = [];
        if (is_string($widget->params) && !empty($widget->params)) {
            $widget->params = unserialize($widget->params);

            foreach ($widget->params as $key => $value) {
                $widgetParams []= [
                    'name' => $key,
                    'value' => $value
                ];
            }
        }
        $widget->params = $widgetParams;
        if (is_null($widget->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a widget with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_widgets'));
        }

        $allInteligentWidgets = \Widget::getAllInteligentWidgets();

        $_SESSION['from'] = $request->server->get("HTTP_REFERER").'?'.$request->getQueryString();

        return $this->render(
            'widget/new.tpl',
            array(
                'all_widgets'  => $allInteligentWidgets,
                'id'           => $id,
                'widget'       => $widget,
                'page'         => $page,
                'category'     => $category
            )
        );
    }

    /**
     * Create a new widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('WIDGET_CREATE')")
     *
     * @CheckModuleAccess(module="WIDGET_MANAGER")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $post = $request->request;

            $widgetData = array(
                'id'             => $post->getDigits('id'),
                'action'         => $post->filter('action', null, FILTER_SANITIZE_STRING),
                'title'          => $post->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'content_status' => (int) $post->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'renderlet'      => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
                'metadata'       => $post->filter('metadata', null, FILTER_SANITIZE_STRING),
                'description'    => $post->get('description', ''),
                'content'        => $post->filter('content', ''),
                'params'          => json_decode($post->get('parsedParams', null)),
            );
            if ($widgetData['renderlet'] == 'intelligentwidget') {
                $widgetData['content'] = $post->filter('intelligent_type', null, FILTER_SANITIZE_STRING);
            }

            if (count($widgetData['params']) > 0) {
                $newParams = [];
                foreach ($widgetData['params'] as $param) {
                    $newParams [$param->name]= $param->value;
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
            $allInteligentWidgets = \Widget::getAllInteligentWidgets();

            return $this->render(
                'widget/new.tpl',
                array(
                    'all_widgets' => $allInteligentWidgets,
                    'action'      => 'new',
                )
            );
        }
    }

    /**
     * Update an existing widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('WIDGET_UPDATE')")
     *
     * @CheckModuleAccess(module="WIDGET_MANAGER")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        $post = $request->request;

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add('error', _("Widget data sent not valid."));

            return $this->redirect($this->generateUrl('admin_widget_show', array('id' => $id)));
        }

        $widgetData = array(
            'id'              => $id,
            'action'          => $post->filter('action', null, FILTER_SANITIZE_STRING),
            'title'           => $post->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'content_status'  => (int) $post->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'renderlet'       => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
            'metadata'        => $post->filter('metadata', null, FILTER_SANITIZE_STRING),
            'description'     => $post->get('description', ''),
            'content'         => $post->filter('content', ''),
            'intelligentType' => $post->filter('intelligent_type', null, FILTER_SANITIZE_STRING),
            'params'          => json_decode($post->get('parsedParams', null)),
        );

        if (count($widgetData['params']) > 0) {
            $newParams = [];
            foreach ($widgetData['params'] as $param) {
                $newParams [$param->name]= $param->value;
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
                $this->generateUrl('admin_widgets', array('page' => $page,))
            );
        }

        $this->get('session')->getFlashBag()->add(
            'success',
            _('Widget updated successfully.')
        );

        return $this->redirect(
            $this->generateUrl('admin_widget_show', array('id' => $id,))
        );
    }

    /**
     * The content provider for widget
     *
     * @param  Request  $request the request object
     * @return Response          the response object
     *
     * @CheckModuleAccess(module="WIDGET_MANAGER")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory((int)$categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'widget')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $widgets      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countWidgets = $em->countBy($filters);

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

        return $this->render(
            'widget/content-provider.tpl',
            [
                'widgets'    => $widgets,
                'pagination' => $pagination,
            ]
        );
    }
}
