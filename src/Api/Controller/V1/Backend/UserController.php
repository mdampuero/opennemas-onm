<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes users.
 */
class UserController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'USER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_user_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'USER_CREATE',
        'delete' => 'USER_DELETE',
        'list'   => 'USER_ADMIN',
        'patch'  => 'USER_UPDATE',
        'save'   => 'USER_CREATE',
        'show'   => 'USER_UPDATE',
        'update' => 'USER_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.user';

    /**
     * {@inheritdoc}
     *
     * This action is overwritten because it's used in edit profile action that
     * should be available to all users with or without having users module
     * activated.
     */
    public function updateItemAction(Request $request, $id)
    {
        if ($id != $this->getUser()->id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        $msg = $this->get('core.messenger');

        $this->get('api.service.user')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $languages = array_merge(
            [ 'default' => _('Default system language') ],
            $this->get('core.locale')->getAvailableLocales()
        );

        return [
            'categories'  => $this->getCategories(),
            'client'      => $this->getClient(),
            'countries'   => $this->get('core.geo')->getCountries(),
            'languages'   => $languages,
            'photos'      => $this->getPhotos($items),
            'taxes'       => $this->get('vat')->getTaxes(),
            'user_groups' => $this->getUserGroups()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategories($items = null)
    {
        return $this->get('api.service.category')->responsify(
            $this->get('api.service.category')
                ->getList()['items']
        );
    }

    /**
     * Returns the list of photos for all items in the list.
     *
     * @param mixed $items The item or the list of items.
     *
     * @return array The list of photos.
     */
    protected function getPhotos($items = null) : array
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = array_filter(array_map(function ($a) {
            return [ 'photo', $a->avatar_img_id ];
        }, $items), function ($a) {
            return !empty($a[1]);
        });

        $photos = $this->get('entity_repository')->findMulti($ids);

        return $this->get('data.manager.filter')
            ->set($photos)
            ->filter('mapify', [ 'key' => 'pk_photo' ])
            ->get();
    }

    /**
     * Returns the list of user groups.
     *
     * @return array The list of user groups.
     */
    protected function getUserGroups() : array
    {
        $ugs = $this->get('api.service.user_group');

        $userGroups = $ugs->getList()['items'];

        return $ugs->responsify($this->get('data.manager.filter')
            ->set($userGroups)
            ->filter('mapify', [ 'key' => 'pk_user_group'])
            ->get());
    }
}
