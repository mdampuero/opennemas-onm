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
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class WidgetsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('WIDGET_MANAGER');
    }

    /**
     * List widgets.
     *
     * @return Response the response object
     *
     * @Security("has_role('WIDGET_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('widget/list.tpl');
    }

    /**
     * Show a selected Widget by id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('WIDGET_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);
        // Need category to redirect to frontpage manager
        $category = $request->query->get('category', 'home');

        $widget = new \Widget($id);

        if (is_string($widget->params)) {
            $widget->params = unserialize($widget->params);
            if (!is_array($widget->params)) {
                $widget->params = array();
            }
        }
        if (is_null($widget->id)) {
            m::add(sprintf(_('Unable to find a widget with the id "%d"'), $id), m::ERROR);

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
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $post = $request->request;
            $items = $post->get('items');
            $values = $post->get('values');

            $widgetData = array(
                'id'             => $post->getDigits('id'),
                'action'         => $post->filter('action', null, FILTER_SANITIZE_STRING),
                'title'          => $post->filter('title', null, FILTER_SANITIZE_STRING),
                'content_status' => $post->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'renderlet'      => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
                'metadata'       => $post->filter('metadata', null, FILTER_SANITIZE_STRING),
                'description'    => $post->filter('description', null, FILTER_SANITIZE_STRING),
                'content'        => $post->filter('content', null, FILTER_SANITIZE_STRING),
                'params'         => array_combine($items, $values),
            );

            if ($widgetData['renderlet'] == 'intelligentwidget') {
                $widgetData['content'] = $post->filter('intelligent-type', null, FILTER_SANITIZE_STRING);
            }

            try {
                $widget = new \Widget();
                $widget->create($widgetData);
            } catch (\Exception $e) {
                m::add($e->getMessage(), m::ERROR);

                return $this->redirect($this->generateUrl('admin_widget_create'));
            }

            m::add(_('Widget created successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_widgets'));

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
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        $post = $request->request;

        // Check empty data
        if (count($request->request) < 1) {
            m::add(_("Widget data sent not valid."), m::ERROR);

            return $this->redirect($this->generateUrl('admin_widget_show', array('id' => $id)));
        }

        $items = $post->get('items');
        $values = $post->get('values');

        $widgetData = array(
            'id'              => $id,
            'action'          => $post->filter('action', null, FILTER_SANITIZE_STRING),
            'title'           => $post->filter('title', null, FILTER_SANITIZE_STRING),
            'content_status'  => $post->filter('content_status', 0, FILTER_SANITIZE_STRING),
            'renderlet'       => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
            'metadata'        => $post->filter('metadata', null, FILTER_SANITIZE_STRING),
            'description'     => $post->filter('description', null, FILTER_SANITIZE_STRING),
            'content'         => $post->filter('content', null, FILTER_SANITIZE_STRING),
            'intelligentType' => $post->filter('intelligent-type', null, FILTER_SANITIZE_STRING),
            'params'          => array_combine($items, $values),
        );
        if ($widgetData['renderlet'] == 'intelligentwidget' && !empty($widgetData['intelligentType'])) {
            $widgetData['content'] = $widgetData['intelligentType'];
        }
        $widget = new \Widget();
        if (!$widget->update($widgetData)) {
            m::add(_('There was an error while updating the widget.'), m::ERROR);

            return $this->redirect(
                $this->generateUrl('admin_widgets', array('page' => $page,))
            );
        }

        m::add(_('Widget updated successfully.'), m::SUCCESS);

        return $this->redirect(
            $this->generateUrl('admin_widget_show', array('id' => $id,))
        );
    }

    /**
     * The content provider for widget
     *
     * @param  Request  $request the request object
     * @return Response          the response object
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

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countWidgets,
                'fileName'    => $this->generateUrl(
                    'admin_widgets_content_provider',
                    array('category' => $categoryId)
                ).'&page=%d',
            )
        );

        return $this->render(
            'widget/content-provider.tpl',
            array(
                'widgets' => $widgets,
                'pager'   => $pagination,
            )
        );
    }
}
