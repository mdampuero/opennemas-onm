<?php

namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.album';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items)
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('album');
    }
}
