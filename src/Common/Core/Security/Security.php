<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Security;

use Common\ORM\Entity\Instance;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Manages the core security by checking the permissions basing on the instance
 * and the current user.
 */
class Security
{
    /**
     * The list of categories.
     *
     * @var array
     */
    protected $categories;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The list of permissions.
     *
     * @var array
     */
    protected $permissions;

    /**
     * The current authorized user.
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * Returns the current authorized user.
     *
     * @return UserInterface The current authorized user.
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Checks if the current user is an administrator.
     *
     * @return boolean True if the current user is an administrator. False
     *                 otherwise.
     */
    public function isAdmin()
    {
        return $this->user->isAdmin();
    }

    /**
     * Checks if the extension is enabled.
     *
     * @param string $uuid The extension UUID.
     *
     * @return boolean True if the extension is enabled. False otherwise.
     */
    public function isExtensionEnabled($uuid)
    {
        return in_array($uuid, $this->instance->activated_modules);
    }

    /**
     * Checks if the current user is a master user.
     *
     * @return boolean True if the current user is a administrator. False
     *                 otherwise.
     */
    public function isMaster()
    {
        return $this->user->isMaster();
    }

    /**
     * Checks if the permission is granted.
     *
     * @param string $permission The permission to check.
     *
     * @return boolean True if the permission is granted. False otherwise.
     */
    public function isGranted($permission)
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * Changes the current categories.
     *
     * @param array $categories The list of categories.
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Changes the current instance.
     *
     * @param Instance $instance The current instance.
     */
    public function setInstance(Instance $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Changes the current permissions for the user.
     *
     * @param array $permissions The list of permissions.
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Changes the current authorized user.
     *
     * @param UserInterface $user The authorized user.
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}
