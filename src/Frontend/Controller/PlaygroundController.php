<?php

namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class PlaygroundController extends Controller
{
    /**
     * Dispatches the actions through the rest of methods in this class
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function defaultAction(Request $request)
    {
        $action = $request->query->get('action', null);

        if (!is_null($action)) {
            $response = $this->{$action}($request);
            return new Response($response, 200);
        } else {
            return new Response('not valid action', 400);
        }
    }

    /**
     * Tests for session in container
     *
     * @return Response the response object
     */
    public function session()
    {
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Your changes were saved!'
        );

        foreach ($this->get('session')->getFlashBag()->get('notice', array()) as $message) {
            echo "<div class='flash-notice'>$message</div>";
        }
    }

    /**
     * Displays the base/playground.tpl content.
     *
     * @return Response The response object.
     */
    public function template()
    {
        return $this->render('playground.tpl');
    }

    /**
     * Displays a widget basing on the request parameters.
     *
     * @param Request $request The request object.
     *
     * @param Response The response object.
     */
    public function widget(Request $request)
    {
        $name = $request->query->get('name');
        $id   = $request->query->get('id');

        if (empty($id) && empty($name)) {
            return;
        }

        $params = $request->query->all();
        $widget = null;

        unset($params['action'], $params['id'], $params['name']);

        if (!empty($id)) {
            $widget = $this->get('entity_repository')->find('Widget', $id);

            return $widget->render($params);
        }

        $criteria = [
            'join'              => [ [
                'table'         => 'widgets',
                'pk_content'    => [
                    [ 'value'   => 'pk_widget', 'field' => true ]
                ],
            ] ],
            'content'           => [ [ 'value' => $name ] ],
            'content_type_name' => [ [ 'value' => 'widget' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
        ];

        $widget = $this->get('entity_repository')->findOneBy($criteria);

        if (empty($widget)) {
            return;
        }

        return $widget->render($params);
    }
}
