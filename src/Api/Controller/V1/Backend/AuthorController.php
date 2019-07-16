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

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.author';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $photos = [];

        if (!empty($items)) {
            $ids = array_filter(array_map(function ($a) {
                return [ 'photo', $a->avatar_img_id ];
            }, $items), function ($a) {
                return !empty($a[1]);
            });

            $photos = $this->get('entity_repository')->findMulti($ids);
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_photo' ])
                ->get();
        }

        return [ 'photos' => $photos, ];
    }
}
