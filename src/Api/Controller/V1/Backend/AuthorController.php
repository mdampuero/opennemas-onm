<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;

/**
 * Displays, saves, modifies and removes authors.
 */
class AuthorController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'USER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_author_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'AUTHOR_CREATE',
        'delete' => 'AUTHOR_DELETE',
        'list'   => 'AUTHOR_ADMIN',
        'patch'  => 'AUTHOR_UPDATE',
        'save'   => 'AUTHOR_CREATE',
        'show'   => 'AUTHOR_UPDATE',
        'update' => 'AUTHOR_UPDATE',
    ];

    protected $module = 'author';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.author';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $response = [
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ];
        if (empty($items)) {
            return $response;
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $photos = [];

        $ids = array_filter(array_map(function ($user) {
            return $user->avatar_img_id;
        }, $items), function ($photo) {
                return !empty($photo);
        });

        try {
            $photos = $this->get('api.service.content')->getListByIds($ids)['items'];
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_content' ])
                ->get();

            $photos = [ 'photos' => $this->get('api.service.content')->responsify($photos) ];
            return array_merge($response, $photos);
        } catch (GetItemException $e) {
        }
        $photos = [ 'photos' => $photos, ];
        return array_merge($response, $photos);
    }
}
