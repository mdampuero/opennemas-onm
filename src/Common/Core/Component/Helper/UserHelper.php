<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class UserHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the UserHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns the list of photos for all users in the list.
     *
     * @param array $users The list of users.
     *
     * @return array The list of photos.
     */
    public function getPhotos($users)
    {
        if (empty($users) || !is_array($users)) {
            return [];
        }

        $ids = array_map(function ($a) {
            return $a->avatar_img_id;
        }, $users);

        $photos = $this->container->get('entity_repository')->findBy([
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'IN' ] ],
            'content_type_name' => [ [ 'value' => 'photo' ] ],
        ]);

        return $this->container->get('data.manager.filter')
            ->set($photos)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get();
    }
}
