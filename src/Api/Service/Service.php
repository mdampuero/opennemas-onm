<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service;

class Service
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The name of the entities source.
     *
     * This is used in ORM manager and repositories.
     *
     * @var string
     */
    protected $origin = 'instance';

    /**
     * Initializes the UserGroupService.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Converts a user group or a list of user group to a structure
     * returnable in a Response.
     *
     * @param mixed $item The user group or the list of user group.
     *
     * @return mixed The converted user group or list of user group.
     */
    public function responsify($item)
    {
        return $this->container->get('orm.manager')->getConverter('UserGroup')
            ->responsify($item);
    }

    /**
     * Changes the name of the entities source.
     *
     * @param string $origin The name of the source.
     *
     * @return UserGroupService The current service.
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }
}
