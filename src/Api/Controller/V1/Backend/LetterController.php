<?php

namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LetterController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'LETTER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'LETTER_CREATE',
        'delete' => 'LETTER_DELETE',
        'patch'  => 'LETTER_UPDATE',
        'update' => 'LETTER_UPDATE',
        'list'   => 'LETTER_ADMIN',
        'save'   => 'LETTER_CREATE',
        'show'   => 'LETTER_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_letter_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.letter';

    /**
     * {@inheritdoc}
    */
    protected function getExtraData($items = null)
    {
        $extra['tags'] = $this->getTags($items);

        $extraData = parent::getExtraData($items);

        return array_merge($extraData, $extra);
    }
}
