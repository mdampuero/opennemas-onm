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

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_event_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'EVENT_CREATE',
        'delete' => 'EVENT_DELETE',
        'patch'  => 'EVENT_UPDATE',
        'update' => 'EVENT_UPDATE',
        'list'   => 'EVENT_ADMIN',
        'save'   => 'EVENT_CREATE',
        'show'   => 'EVENT_UPDATE',
    ];

    protected $module = 'event';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.content';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        $categories = $this->get('api.service.category')->responsify(
            $this->get('api.service.category')->getList()['items']
        );

        $events = $this->get('core.helper.event')->getEventsGroupedByType();

        return array_merge(parent::getExtraData($items), [
            'categories' => $categories,
            'tags'       => $this->getTags($items),
            'events'     => $events,
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ]);
    }

    /**
     * Get the event config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');
        $sh = $this->get('core.helper.setting');

        $config = $ds->get('event_settings', []);

        $config = $sh->toBoolean($config, ['hide_current_events']);

        return new JsonResponse([
            'config' => $config
        ]);
    }

    /**
     * Saves config for events.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $config = $request->request->get('config', []);
        $config = $this->get('core.helper.setting')
            ->toBoolean($config, ['hide_current_events']);

        try {
            $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->set('event_settings', $config);

            $this->get('core.dispatcher')
                ->dispatch('event.config');
            $msg->add(_('Settings saved.'), 'success', 200);
        } catch (\Exception $e) {
            $msg->add(_('There was an error while saving the settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * gets the preview of the event content.
     *
     * @throws GetItemException If no preview is available.
     * @return Response The response object containing the preview content.
     */
    public function getPreviewAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $session = $this->get('session');
        $content = $session->get('last_preview');

        $session->remove('last_preview');

        return new Response($content);
    }

    /**
     * Saves the preview of the event content.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object containing the status of the save operation.
     */
    public function savePreviewAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        if ($this->get('core.instance') && !$this->get('core.instance')->isSubdirectory()) {
            $this->get('core.locale')->setContext('frontend')
                ->setRequestLocale($request->get('locale'));
        }

        $event = new Content(['pk_content' => 0]);

        $data = $request->request->filter('item');
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if (isset($value) && !empty($value)) {
                $event->{$key} = $value;
            }
        }

        $event = $this->get('data.manager.filter')->set($event)
            ->filter('localize', ['keys' => $this->get($this->service)->getL10nKeys()])
            ->get();

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        list($positions, $advertisements) = $this->getAdvertisements();

        $params = [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'item'           => $event,
            'content'        => $event,
            'contentId'      => $event->pk_content
        ];

        $this->view->assign($params);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('event/item.tpl')
        );

        return new Response('OK');
    }
}
