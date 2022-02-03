<?php

namespace Api\Controller\V1\Backend;

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

    protected $module = 'letter';

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

        return array_merge([
            'tags'    => $this->getTags($items),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ], $extraData);
    }
}
