<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;

/**
 * Displays, saves, modifies and removes user groups.
 */
class UserGroupController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'USER_GROUP_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_user_group_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'GROUP_CREATE',
        'delete' => 'GROUP_DELETE',
        'list'   => 'GROUP_ADMIN',
        'patch'  => 'GROUP_UPDATE',
        'save'   => 'GROUP_CREATE',
        'show'   => 'GROUP_UPDATE',
        'update' => 'GROUP_UPDATE',
    ];

    protected $module = 'userGroup';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.user_group';

    /**
     * Returns the list of extensions.
     *
     * @return array The list of extensions.
     */
    protected function getExtensions($uuids) : array
    {
        $locale = $this->get('core.locale')->getLocaleShort();
        $oql    = sprintf("uuid in ['%s']", implode("','", $uuids));

        $extensions = $this->get('orm.manager')
            ->getRepository('Extension')
            ->findBy($oql);

        $extensions = $this->get('data.manager.filter')
            ->set($extensions)
            ->filter('mapify', [ 'key' => 'uuid' ])
            ->get();

        return array_map(function ($a) use ($locale) {
            return $a->getName($locale);
        }, $extensions);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $permissions = $this->get('core.helper.permission')->getByModule();

        return [
            'extensions' => $this->getExtensions(array_keys($permissions)),
            'modules'    => $permissions,
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item->pk_user_group;
    }
}
