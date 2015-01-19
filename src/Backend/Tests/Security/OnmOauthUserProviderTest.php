<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Backend\Tests\Security;

use Backend\Security\OnmOAuthUserProvider;

class OnmOAuthUserProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Backend\\Security\\OnmOAuthUserProvider')) {
            $this->markTestSkipped('Backend\Security\OnmOAuthUserProvider is not available.');
        }
    }

    /**
     * @covers Backend\Security\OnmOAuthUserProvider::__construct
     */
    public function testConstruct()
    {
        $container = $this
            ->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')
            ->setConstructorArgs(array())
            ->getMock()
        ;

        $userProvider = new OnmOAuthUserProvider($container);

        $this->assertEquals($container, \PHPUnit_Framework_Assert::readAttribute($userProvider, 'container'));
    }

    /**
     * @covers Backend\Security\OnmOAuthUserProvider::loadUserByUsername
     */
    public function testLoadUserByUsername()
    {
        $user = $this
            ->getMockBuilder('\\User')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $userManager = $this
            ->getMockBuilder('Repository\\UserGroupManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $userManager
            ->expects($this->once())
            ->method('findBy')
            ->will($this->returnValue($user))
        ;

        $container = $this
            ->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')
            ->setConstructorArgs(array())
            ->getMock()
        ;

        $container
            ->expects($this->once())
            ->method('get')
            ->with('user_repository')
            ->will($this->returnValue($userManager))
        ;

        $userProvider = new OnmOAuthUserProvider($container);

        $user = $userProvider->loadUserByUsername('fran');

        $this->assertTrue(is_object($user));
    }

    /**
     * @covers Backend\Security\OnmOAuthUserProvider::loadUserByUsername
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameWithNotValidUser()
    {
        $userManager = $this
            ->getMockBuilder('Repository\\UserGroupManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $userManager
            ->expects($this->once())
            ->method('findBy')
            ->will($this->returnValue(null))
        ;

        $container = $this
            ->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')
            ->setConstructorArgs(array())
            ->getMock()
        ;

        $container
            ->expects($this->once())
            ->method('get')
            ->with('user_repository')
            ->will($this->returnValue($userManager))
        ;

        $userProvider = new OnmOAuthUserProvider($container);

        $user = $userProvider->loadUserByUsername('fran');

        $this->assertTrue(is_object($user));
    }

    /**
     * @covers Backend\Security\OnmOAuthUserProvider::supportsClass
     */
    public function testSupportsClass()
    {
        $container = $this
            ->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')
            ->setConstructorArgs(array())
            ->getMock()
        ;
        $userProvider = new OnmOAuthUserProvider($container);

        $this->assertTrue($userProvider->supportsClass('User'));
    }
}
