<?php

namespace Api\Controller\V1\Backend;

class ObituaryController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'OBITUARY_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'OBITUARY_CREATE',
        'delete' => 'OBITUARY_DELETE',
        'patch'  => 'OBITUARY_UPDATE',
        'update' => 'OBITUARY_UPDATE',
        'list'   => 'OBITUARY_ADMIN',
        'save'   => 'OBITUARY_CREATE',
        'show'   => 'OBITUARY_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_obituary_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.obituary';

    /**
     * Loads extra data related to the given contents.
     *
     * @param array $items The items array
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);

        return array_merge([ 'tags' => $this->getTags($items) ], $extra);
    }

    /**
     * {@inheritdoc}
     */
    public function getL10nKeys()
    {
        //TODO: Check what multilanguage keys needs
        return $this->get($this->service)->getL10nKeys('article');
    }
}
