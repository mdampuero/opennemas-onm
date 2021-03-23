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
