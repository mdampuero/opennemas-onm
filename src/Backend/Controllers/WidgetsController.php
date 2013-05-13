<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class WidgetsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * List available widgets
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page', 20);

        $cm = new \ContentManager();
        list($countWidgets, $widgets) = $cm->getCountAndSlice(
            'widget',
            null,
            '',
            'ORDER BY title ASC',
            $page,
            $itemsPerPage
        );

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
                    'admin_widgets'
                ).'?page=%d',
            )
        );

        return $this->render(
            'widget/list.tpl',
            array(
                'widgets'    => $widgets,
                'pagination' => $pagination,
                'page'       => $page,
            )
        );
    }

    /**
     * Show a selected Widget by id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);
        // Need category to redirect to frontpage manager
        $category = $request->query->get('category', 'home');

        $widget = new \Widget($id);

        if (is_null($widget->id)) {
            m::add(sprintf(_('Unable to find a widget with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_widgets'));
        }

        $allInteligentWidgets = \Widget::getAllInteligentWidgets();

        $_SESSION['from'] = $request->server->get("HTTP_REFERER").'?'.$request->getQueryString();

        return $this->render(
            'widget/new.tpl',
            array(
                'all_widgets' => $allInteligentWidgets,
                'id'          => $id,
                'widget'      => $widget,
                'page'        => $page,
                'category'    => $category
            )
        );
    }

    /**
     * Delete a selected widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        $widget = new \Widget();
        $widget->delete($id);

        return $this->redirect($this->generateUrl('admin_widgets'));
    }

    /**
     * Create a new widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $post = $request->request;

            $widgetData = array(
                'id'          => $post->getDigits('id'),
                'action'      => $post->filter('action', null, FILTER_SANITIZE_STRING),
                'title'       => $post->filter('title', null, FILTER_SANITIZE_STRING),
                'available'   => $post->filter('available', null, FILTER_SANITIZE_STRING),
                'renderlet'   => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
                'metadata'    => $post->filter('metadata', null, FILTER_SANITIZE_STRING),
                'description' => $post->filter('description', null, FILTER_SANITIZE_STRING),
                'content'     => $post->filter('content', null, FILTER_SANITIZE_STRING),
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
     **/
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

        $widgetData = array(
            'id'              => $id,
            'action'          => $post->filter('action', null, FILTER_SANITIZE_STRING),
            'title'           => $post->filter('title', null, FILTER_SANITIZE_STRING),
            'available'       => $post->filter('available', null, FILTER_SANITIZE_STRING),
            'renderlet'       => $post->filter('renderlet', null, FILTER_SANITIZE_STRING),
            'metadata'        => $post->filter('metadata', null, FILTER_SANITIZE_STRING),
            'description'     => $post->filter('description', null, FILTER_SANITIZE_STRING),
            'content'         => $post->filter('content', null, FILTER_SANITIZE_STRING),
            'intelligentType' => $post->filter('intelligent-type', null, FILTER_SANITIZE_STRING),
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
     * Change the availability of a Widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function toogleAvailableAction(Request $request)
    {
        $id = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        $widget = new \Widget($id);

        if (is_null($widget->id)) {
            m::add(sprintf(_('Unable to find widget with id "%d"'), $id), m::ERROR);
        } else {
            $available = ($widget->available+1) % 2;
            $widget->set_available($available, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed availability for "%s" widget'), $widget->title), m::SUCCESS);
        }

        return $this->redirect(
            $this->generateUrl('admin_widgets', array('page' => $page,))
        );
    }

    /**
     * The content provider for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        if ($category == 'home') {
            $category = 0;
        }

        $cm = new  \ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsIdsForHomepageOfCategory($category);

        // Fetching opinions
        $sqlExcludedOpinions = '';
        if (count($contentElementsInFrontpage) > 0) {
            $contentsExcluded = implode(', ', $contentElementsInFrontpage);
            $sqlExcludedOpinions = ' AND `pk_widget` NOT IN ('.$contentsExcluded.')';
        }

        list($countWidgets, $widgets) = $cm->getCountAndSlice(
            'Widget',
            null,
            'contents.available=1 AND contents.in_litter != 1 '.$sqlExcludedOpinions,
            'ORDER BY created DESC ',
            $page,
            8
        );

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
                    array('category' => $category)
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
