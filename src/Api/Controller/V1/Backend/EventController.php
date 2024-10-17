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
use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

        return array_merge(parent::getExtraData($items), [
            'categories' => $categories,
            'tags'       => $this->getTags($items),
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
        $ds  = $this->get('orm.manager')->getDataSet('Settings', 'instance');
        $sh  = $this->get('core.helper.setting');

        $config = $request->request->get('config', []);
        $config = $sh->toBoolean($config, ['hide_current_events']);

        try {
            $ds->set('event_settings', $config);

            $this->get('core.dispatcher')
                ->dispatch('events.config');

            $this->get('api.service.redis')->deleteItemByPattern('events-*');
            $msg->add(_('Settings saved.'), 'success', 200);
        } catch (\Exception $e) {
            $msg->add(_('There was an error while saving the settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
