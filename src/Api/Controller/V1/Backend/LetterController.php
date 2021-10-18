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

    /**
     * Returns the list of tags for an item or a list of items.
     *
     * @param mixed $items The item or the list of items to get tags for.
     *
     * @return array The list of tags.
     */
    protected function getTags($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = [];

        foreach ($items as $item) {
            if (!empty($item->tags)) {
                $ids = array_unique(array_merge($ids, $item->tags));
            }
        }

        $ids = array_values(array_filter($ids, function ($a) {
            return !empty($a);
        }));

        if (empty($ids)) {
            return [];
        }

        return $this->get('api.service.tag')->responsify(
            $this->get('api.service.tag')->getListByIds($ids)['items']
        );
    }
}
