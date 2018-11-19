<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Security\User;

use Common\Core\Component\Security\User\UserProvider;
use Common\ORM\Entity\User;

/**
 * Defines test cases for UserProvider class.
 */
class UserProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->repository = $this->getMockBuilder('\Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('\Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->provider = new UserProvider($this->em, [ 'flob' ]);
    }

    /**
     * Tests loadUserByUsername.
     */
    public function testLoadUserByUsername()
    {
        $user = new User([ 'id' => 1 ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('username = "wibble" or email = "wibble"')
            ->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByUsername('wibble'));
    }

    /**
     * Tests loadUserByUsername when no users found.
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameWhenNoUsersFound()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->will($this->throwException(new \Exception()));

        $this->provider->loadUserByUsername('wibble');
    }

    /**
     * Tests refreshUser.
     */
    public function testRefreshUser()
    {
        $user = new User([ 'id' => 1 ]);

        $this->assertEquals($user, $this->provider->refreshUser($user));
    }

    /**
     * Tests supportClass with valid and invalid class names.
     */
    public function testSupportClass()
    {
        $this->assertFalse($this->provider->supportsClass('Wubble'));
        $this->assertTrue($this->provider->supportsClass('User'));
    }
}
