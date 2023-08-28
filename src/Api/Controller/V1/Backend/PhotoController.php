<?php

namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PhotoController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'IMAGE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_photo_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'PHOTO_CREATE',
        'delete' => 'PHOTO_DELETE',
        'patch'  => 'PHOTO_UPDATE',
        'update' => 'PHOTO_UPDATE',
        'list'   => 'PHOTO_ADMIN',
        'save'   => 'PHOTO_CREATE',
        'show'   => 'PHOTO_UPDATE',
    ];

    protected $module = 'photo';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.photo';

    /**
     * {@inheritdoc}
     */
    public function saveItemAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $this->checkSecurity($this->extension, $this->getActionPermission('save'));
        $files = $request->files->all();
        $file  = array_pop($files);
        $data  = $request->request->all();
        $item  = $this->get($this->service)->createItem($data, $file);

        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        if (!empty($this->getItemRoute)) {
            $response->headers->set('Location', $this->generateUrl(
                $this->getItemRoute,
                [ 'id' => $this->getItemId($item) ]
            ));
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('photo');
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ]);
    }

    /**
     * Returns photos configuration
     *
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');
        $sh = $this->get('core.helper.setting');

        $config = $ds->get('photo_settings', []);
        $config = $sh->toBoolean($config, ['optimize_images']);

        $config['image_quality']    = $config['image_quality'] ?? '65';
        $config['image_resolution'] = $config['image_resolution'] ?? '1920x1080';
        return new JsonResponse([
            'config' => $config
        ]);
    }

    /**
     * Returns comments configuration
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

        $config = $sh->toInt($config, ['number_elements']);

        try {
            $ds->set('photo_settings', $config);

            $this->get('core.dispatcher')
                ->dispatch('photos.config');

            $msg->add(_('Settings saved.'), 'success', 200);
        } catch (\Exception $e) {
            $msg->add(_('There was an error while saving the settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
