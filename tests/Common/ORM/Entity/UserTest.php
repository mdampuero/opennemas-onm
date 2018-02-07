<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Core;

use Common\ORM\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->user = new User([
            'activated'     => true,
            'fk_user_group' => [],
            'username'      => 'waldo',
            'password'      => 'glorp',
            'token'         => 'baz',
        ]);
    }

    /**
     * Tests equals for users with same and different usernames.
     */
    public function testEquals()
    {
        $user = new User(['username' => 'waldo' ]);
        $this->assertTrue($this->user->equals($user));

        $user = new User(['username' => 'fubar' ]);
        $this->assertFalse($this->user->equals($user));
    }

    /**
     * Tests eraseCredentials.
     */
    public function testEraseCredentials()
    {
        $this->assertNotEmpty($this->user->password);
        $this->assertNotEmpty($this->user->token);

        $this->user->eraseCredentials();

        $this->assertEmpty($this->user->password);
        $this->assertEmpty($this->user->roles);
        $this->assertEmpty($this->user->token);
    }

    /**
     * Tests getRoles.
     */
    public function testGetRoles()
    {
        $this->user->type = 1;
        $this->assertTrue(in_array('ROLE_FRONTEND', $this->user->getRoles()));
        $this->assertFalse(in_array('ROLE_BACKEND', $this->user->getRoles()));

        $this->user->type = 0;
        $this->assertTrue(in_array('ROLE_BACKEND', $this->user->getRoles()));
        $this->assertFalse(in_array('ROLE_MANAGER', $this->user->getRoles()));

        $this->user->setOrigin('manager');
        $this->assertTrue(in_array('ROLE_MANAGER', $this->user->getRoles()));
    }

    /**
     * Test getPassword.
     */
    public function testGetPassword()
    {
        $this->assertEquals($this->user->password, $this->user->getPassword());
    }

    /**
     * Test getPayload.
     */
    public function testGetPayload()
    {
        $data = $this->user->getData();

        unset($data['password']);
        unset($data['token']);
        unset($data['roles']);

        $this->assertEquals($data, $this->user->getPayload());
    }

    /**
     * Test getSalt.
     */
    public function testGetSalt()
    {
        $this->assertEmpty($this->user->getSalt());
    }

    /**
     * Test getUsername.
     */
    public function testGetUsername()
    {
        $this->assertEquals($this->user->username, $this->user->getUsername());
    }

    /**
     * Tests isAccountNonExpired.
     */
    public function testIsAccountNonExpired()
    {
        $this->assertTrue($this->user->isAccountNonExpired());
    }

    /**
     * Tests isAccountNonLocked.
     */
    public function testIsAccountNonLocked()
    {
        $this->assertTrue($this->user->isAccountNonLocked());
    }

    /**
     * Tests isCredentialsNonExpired.
     */
    public function testIsCredentialsNonExpired()
    {
        $this->assertTrue($this->user->isCredentialsNonExpired());
    }

    /**
     * Tests isEnabled.
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->user->isEnabled());

        $this->user->activated = false;
        $this->assertFalse($this->user->isEnabled());
    }

    /**
     * Tests isEqualsTo.
     */
    public function testIsEqualTo()
    {
        $user = new User([ 'username' => 'garply', 'fk_user_group' => [] ]);

        $this->assertFalse($this->user->isEqualTo($user));

        $user->username = 'waldo';
        $this->assertTrue($this->user->isEqualTo($user));
    }
}
