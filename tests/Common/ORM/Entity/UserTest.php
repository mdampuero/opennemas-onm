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
            'email'         => 'waldo@baz.com',
            'fk_user_group' => [],
            'password'      => 'glorp',
            'token'         => 'baz',
            'username'      => 'waldo',
        ]);
    }

    /**
     * Tests equals for users with same and different usernames.
     */
    public function testEquals()
    {
        $user = new User(['email' => 'waldo@baz.com' ]);
        $this->assertTrue($this->user->equals($user));

        $user = new User(['email' => 'fubar@gorp.org' ]);
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
        $this->assertEquals([ 'ROLE_USER' ], $this->user->getRoles());

        $this->user->type = 0;
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
        $this->assertNotContains('ROLE_MASTER', $this->user->getRoles());

        $this->user->setOrigin('manager');
        $this->assertContains('ROLE_MASTER', $this->user->getRoles());
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
        $this->assertEquals($this->user->email, $this->user->getUsername());
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
        $user = new User([ 'email' => 'garply@xyzzy.gar', 'fk_user_group' => [] ]);

        $this->assertFalse($this->user->isEqualTo($user));

        $user->email = 'waldo@baz.com';
        $this->assertTrue($this->user->isEqualTo($user));
    }
}
