<?php

namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AlbumController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_album_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'ALBUM_CREATE',
        'delete' => 'ALBUM_DELETE',
        'patch'  => 'ALBUM_UPDATE',
        'update' => 'ALBUM_UPDATE',
        'list'   => 'ALBUM_ADMIN',
        'save'   => 'ALBUM_CREATE',
        'show'   => 'ALBUM_UPDATE',
    ];

    protected $module = 'album';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.album';

    /**
     * Get the albums config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, 'ALBUM_SETTINGS');

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get(['album_layout', 'album_max']);

        return new JsonResponse([
            'album_layout' => $settings['album_layout'],
            'album_max'    => $settings['album_max'],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items),
            'max_photos' => (int) $this->get('orm.manager')
                ->getDataSet('Settings')
                ->get('album_max', 100),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('album');
    }

    /**
     * Saves configuration for albums.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'ALBUM_SETTINGS');

        $settings = [
            'album_layout' => $request->request->get('album_layout'),
            'album_max'    => $request->request->get('album_max')
        ];

        $msg = $this->get('core.messenger');

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
