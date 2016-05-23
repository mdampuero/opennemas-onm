<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Entity;

use Common\ORM\Core\Entity;
use Lexik\Bundle\JWTAuthenticationBundle\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The Userclass represents an Opennemas user group.
 */
class User extends Entity implements AdvancedUserInterface, EquatableInterface, JWTUserInterface
{
    /**
     * Returns whether or not the given user is equivalent to this user.
     *
     * @return boolean
     */
    public function equals(UserInterface $user)
    {
        if ($user->getUsername() === $this->getUsername()) {
            return true;
        }

        return false;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
        unset($this->data['roles']);
        unset($this->data['password']);
        unset($this->data['token']);
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return array The user roles
     */
    public function getRoles()
    {
        return ['ROLE_BACKEND'];
        if (!isset($this->roles)) {
            if (in_array('4', $this->id_user_group)
                || in_array('5', $this->id_user_group)
            ) {
                $this->roles = \Privilege::getPrivilegeNames();

                if (in_array('4', $this->id_user_group)) {
                    $this->roles[] = 'ROLE_MASTER';
                }

                if (in_array('5', $this->id_user_group)) {
                    $this->roles[] = 'ROLE_ADMIN';
                }
            } else {
                $this->roles = array();
                foreach ($this->id_user_group as $group) {
                    $groupPrivileges = \Privilege::getPrivilegesForUserGroup($group);
                    $this->roles = array_merge(
                        $this->roles,
                        $groupPrivileges
                    );
                }
            }

            if ((int) $this->type == 0) {
                $this->roles[] = 'ROLE_BACKEND';
            } else {
                $this->roles[] = 'ROLE_FRONTEND';
            }
        }

        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        $this->eraseCredentials();

        return $this->getData();
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * @return null The salt.
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * @return boolean
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * @return boolean
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * @return boolean
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isMaster() || $this->enabled;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param  UserInterface $user
     * @return boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User
            && $this->getUsername() === $this->getUsername()
        ) {
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach ($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }
            return $isEqual;
        }

        return false;
    }

    /**
     * Returns whether or not user is in master group.
     *
     * @return boolean True if the users is in master group.
     */
    public function isMaster()
    {
        if (in_array('4', $this->user_group_ids)) {
            return true;
        }

        return false;
    }

    /**
     * Returns whether or not user is in administrator group.
     *
     * @return boolean True if the users is in administrator group.
     */
    public function isAdmin()
    {
        if (in_array('4', $this->id_user_group)
            || in_array('5', $this->id_user_group)
        ) {
            return true;
        }

        return false;
    }
}
