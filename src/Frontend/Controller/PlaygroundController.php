<?php

namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Opennemas\Data\Type\Str;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $action = Str::camelCase($action);

        if (empty($action) || !method_exists($this, $action)) {
            return new Response('Not a valid action', 400);
        }

        return $this->{$action}($request);
    }

    /**
     * Returns a fake JSON response with the message and the status code
     * specified as parameters.
     *
     * @param Request $request The request object.
     *
     * @return JsonRseponse The response object.
     */
    private function jsonResponse($request)
    {
        $message = $request->get('message');
        $code    = $request->get('code', 200);

        return new JsonResponse([
            'code'    => $code,
            'message' => $message
        ], $code);
    }

    /**
     * Returns a fake response with the message and the status code specified
     * as parameters.
     *
     * @param Request $request The request object.
     *
     * @return Rseponse The response object.
     */
    private function response($request)
    {
        $message = $request->get('message');
        $code    = $request->get('code', 200);

        return new Response($message, $code);
    }

    /**
     * Tests for session in container.
     *
     * @return Response The response object.
     */
    private function session()
    {
        $this->get('session')->getFlashBag()
            ->add('notice', 'Your changes were saved!');

        $html = '';

        foreach ($this->get('session')->getFlashBag()->get('notice', []) as $message) {
            $html .= "<div class='flash-notice'>$message</div>";
        }

        return new Response($html, 200);
    }

    /**
     * Displays the playground.tpl content.
     *
     * @return Response The response object.
     */
    private function template()
    {
        return new Response($this->render('playground.tpl'), 200);
    }

    private function testConnection()
    {
        try {
            $service  = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');
            $webpush  = $this->get(sprintf('external.web_push.factory.%s', $service));
            $endpoint = $webpush->getEndpoint('test_connection');
            $endpoint->testConnection();
            dump('OK');
            die();
        } catch (\Exception $e) {
            dump($e);
            die();
        }
    }

    private function getSubscribers()
    {
        try {
            $service     = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');
            $webpush     = $this->get(sprintf('external.web_push.factory.%s', $service));
            $endpoint    = $webpush->getEndpoint('subscriber');
            $subscribers = $endpoint->getSubscribers();
            dump($subscribers);
            die();
        } catch (\Exception $e) {
            dump($e);
            die();
        }
    }

    private function sendNotification()
    {
        try {
            $service = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');

            $webpush              = $this->get(sprintf('external.web_push.factory.%s', $service));
            $notificationEndpoint = $webpush->getEndpoint('notification');

            $webpushHelper    = $this->get(sprintf('core.helper.%s', $service));
            $notificationData = $webpushHelper->getNotificationData('870922');
            $sentNotification = $notificationEndpoint->sendNotification([ 'data' => $notificationData ]);
            dump($sentNotification);
            die();
        } catch (\Exception $e) {
            dump($e);
            die();
        }
    }

    private function sendPulse()
    {
        try {
            // $contentService = $this->get('api.service.content');
            // $photoHelper    = $this->get('core.helper.photo');

            // $favico = $contentService->getItem(
            //     $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('logo_favico')
            // );

            // $favicoPath  = $photoHelper->getPhotoPath($favico, null, [], true);

            // $curl = curl_init();
            // curl_setopt($curl, CURLOPT_URL, $favicoPath);
            // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($curl, CURLOPT_HEADER, false);
            // $iconContent = curl_exec($curl);
            // curl_close($curl);

            // dump($favicoPath);
            // dump($iconContent);
            // die();
            // $service = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');

            // $webpushHelper        = $this->get(sprintf('core.helper.%s', $service));
            // $webpush              = $this->get(sprintf('external.web_push.factory.%s', $service));
            // $notificationEndpoint = $webpush->getEndpoint('subscriber');
            // $notificationData     = $notificationEndpoint->getSubscribers(
            //     $webpushHelper->prepareDataForEndpoint('subscriber')
            // );
            // dump($notificationData);
            // $weblist = $webpush->getEndpoint('website');
            // $list = $weblist->getList();
            // dump($list);
            // die();
            // $notificationEndpoint = $webpush->getEndpoint('notification');
            // $webpushHelper        = $this->get(sprintf('core.helper.%s', $service));
            // $notificationData     = $webpushHelper->getNotificationData(870929);
            // dump($notificationData);
            // $sentNotification     = $notificationEndpoint->sendNotification([ 'data' => $notificationData ]);
            // $service  = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('webpush_service');
            // $webpush  = $this->get(sprintf('external.web_push.factory.%s', $service));
            // $endpoint = $webpush->getEndpoint('test_connection');
            // $result   = $endpoint->testConnection();
            // $sendpulseHelper = $this->get('core.helper.sendpulse');
            // $sendpulse       = $this->get('external.web_push.factory.sendpulse');
            // $endpoint        = $sendpulse->getEndpoint('code_snippet');
            // $snippet         = $endpoint->getCode([ 'id' => $sendpulseHelper->getWebsiteId() ]);
            // dump($sentNotification);
            // die();
            // $mainDomain = $this->get('core.instance')->getMainDomain();
            // $mainDomain = 'verdadesymentiras.com';
            // dump($mainDomain);

            // $webId = null;
            // foreach ($websiteList as $website) {
            //     if (strpos($mainDomain, $website['url']) !== false) {
            //         $webId = $website['id'];
            //         break;
            //     }
            // }
            // dump($webId);
            // $this->get('orm.manager')
            //     ->getDataSet('Settings', 'instance')
            //     ->set('sendpulse_website_id', $webId);

            return new Response('OK', 200);
        } catch (\Exception $e) {
            dump($e);
            die();
        }
    }
    /**
     * Displays a widget basing on the request parameters.
     *
     * @param Request $request The request object.
     *
     * @param Response The response object.
     */
    private function widget(Request $request)
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

            return new Response(
                $this->get('frontend.renderer.widget')->render($widget, $params),
                200
            );
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
            return new Response('Widget not found', 404);
        }

        return new Response(
            $this->get('frontend.renderer.widget')->render($widget, $params),
            200
        );
    }
}
