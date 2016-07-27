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
    protected $categories = [];

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

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
     * Checks if the current user has the role.
     *
     * @param string $role The role to check.
     *
     * @return boolean True if the current user has the role. False otherwise.
     */
    public function hasRole($role)
    {
        if (empty($this->user->getRoles())) {
            return false;
        }

        return in_array($role, $this->user->getRoles());
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
     * Checks if the permission is granted.
     *
     * @param string $permission The permission to check.
     *
     * @return boolean True if the permission is granted. False otherwise.
     */
    public function isGranted($permission)
    {
        if (empty($this->user->privileges)) {
            return false;
        }

        return in_array($permission, $this->user->privileges);
    }

    /**
     * Changes the current categories.
     *
     * @param array $categories The list of categories.
     */
    public function setCategories($categories)
    {
        if (is_null($categories)) {
            $categories = [];
        }

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
     * Changes the current authorized user.
     *
     * @param UserInterface $user The authorized user.
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}
