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
use Common\Model\Entity\User;

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
        $this->cache = $this->getMockBuilder('CacheConnection')
            ->setMethods([ 'set' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('DatabaseConnection')
            ->setMethods([ 'getData' ])
            ->getMock();

        $this->cm = $this->getMockBuilder('Opennemas\Cache\Core\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->cm->expects($this->any())->method('getConnection')
            ->willReturn($this->cache);

        $this->repository = $this->getMockBuilder('Opennemas\Orm\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository', 'getConnection' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->em->expects($this->any())->method('getConnection')
            ->willReturn($this->conn);

        $this->provider = new UserProvider($this->em, $this->cm, [ 'flob' ]);
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

        $this->em->expects($this->any())->method('getConnection')
            ->willReturn($this->repository);

        $this->conn->expects($this->any())->method('getData')
            ->willReturn([ 'dbname' => 'quux' ]);

        $this->assertEquals($user, $this->provider->loadUserByUsername('wibble'));
    }

    /**
     * Tests loadUserByUsername from manager (onm-instances).
     */
    public function testLoadUserByUsernameFromManager()
    {
        $user = new User([ 'id' => 1 ]);
        $user->setOrigin('instance');

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('username = "wibble" or email = "wibble"')
            ->willReturn($user);

        $this->em->expects($this->any())->method('getConnection')
            ->willReturn($this->repository);

        $this->conn->expects($this->any())->method('getData')
            ->willReturn([ 'dbname' => 'onm-instances' ]);

        $this->cache->expects($this->any())->method('set')
            ->with('user-1', $user);

        $this->assertEquals($user, $this->provider->loadUserByUsername('wibble'));
        $this->assertEquals('manager', $user->getOrigin());
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
        $this->assertTrue($this->provider->supportsClass('Common\Model\Entity\User'));
    }
}
