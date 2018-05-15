<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security;

use Common\ORM\Entity\Instance;
use Common\ORM\Entity\User;
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
     * The list of instances.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * The list of permissions.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * The current authorized user.
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * Returns the current categories.
     *
     * @return array The current categories.
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Returns the list of instances.
     *
     * @return array The list of instances.
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * Returns the current permissions.
     *
     * @return array The current permissions.
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

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
     * Checks if the category is allowed.
     *
     * @param string $category The category to check.
     *
     * @return boolean True if the category is allowed. False otherwise.
     */
    public function hasCategory($category)
    {
        if (empty($category)) {
            return true;
        }

        if ($this->hasPermission('MASTER')
            || (!empty($this->user)
                && $this->user->isAdmin())
        ) {
            return true;
        }

        if (empty($this->categories)) {
            return true;
        }

        return in_array($category, $this->categories);
    }
    /**
     * Checks if the extension is enabled.
     *
     * @param string $uuid The extension to check.
     *
     * @return boolean True if the extension is enabled. False otherwise.
     */
    public function hasExtension($uuid)
    {
        return in_array($uuid, $this->instance->activated_modules);
    }

    /**
     * Checks if the user owns the instance.
     *
     * @param string $name The instance name.
     *
     * @return boolean True if the user owns the instance. False otherwise.
     */
    public function hasInstance($name)
    {
        if ($this->hasPermission('MASTER')) {
            return true;
        }

        if ($name === 'manager' && $this->hasPermission('PARTNER')) {
            return true;
        }

        return in_array($name, $this->instances);
    }

    /**
     * Checks if the permission is granted.
     *
     * @param string $permission The permission to check.
     *
     * @return boolean True if the permission is granted. False otherwise.
     */
    public function hasPermission($permission)
    {
        if (in_array('MASTER', $this->permissions)) {
            return true;
        }

        // ADMIN and PARTNER have all permissions for their instances
        // TODO: Remove isAdmin when using ADMIN permission for administrators
        if ($this->instance->internal_name !== 'manager'
            && $permission !== 'MASTER'
            && (in_array('ADMIN', $this->permissions)
                || (!empty($this->user) && $this->user->isAdmin())
                || (in_array('PARTNER', $this->permissions)
                    && $this->hasInstance($this->instance->internal_name)
                )
            )
        ) {
            return true;
        }

        return in_array($permission, $this->permissions);
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
     * Sets an empty user for console commands.
     *
     * TODO: Remove when no user usage in data model
     */
    public function setCliUser()
    {
        $this->user = new User([ 'id' => 0, 'username' => 'cli' ]);
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
     * Changes the list of instances.
     *
     * @param array $instances The list of instances.
     */
    public function setInstances($instances)
    {
        $this->instances = $instances;
    }

    /**
     * Changes the current permissions.
     *
     * @param array $permissions The list of permissions.
     */
    public function setPermissions($permissions)
    {
        if (is_null($permissions)) {
            $permissions = [];
        }

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
