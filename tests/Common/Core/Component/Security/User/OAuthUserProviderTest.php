<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Security\User;

use Common\ORM\Entity\User;
use Common\Core\Component\Security\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

/**
 * Defines test cases for OAuthUserProvider.
 */
class OnmOAuthUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    protected function setUp()
    {
        $this->repository = $this->getMockBuilder('\Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'find', 'findOneBy' ])
            ->getMock();

        $this->em = $this->getMockBuilder('\Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRepository', 'persist' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')->willReturn($this->repository);

        $this->provider = new OAuthUserProvider($this->em, $this->session, [ 'wobble' ]);
    }

    /**
     * Tests loadUserByOAuthUserResponse when user found.
     */
    public function testLoadUserByOAuthUserResponse()
    {
        $user = new User([ 'id' => 1]);

        $resource = $this->getMockBuilder('Resource')
            ->setMethods([ 'getName' ])
            ->getMock();

        $response = $this->getMockBuilder('\HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMockForAbstractClass();

        $resource->expects($this->any())->method('getName')->willReturn('norf');
        $response->expects($this->any())->method('getResourceOwner')->willReturn($resource);
        $response->expects($this->any())->method('getUserName')->willReturn('glorp');

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('norf_id = "glorp"')->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByOAuthUserResponse($response));
    }

    /**
     * Tests loadUserByOAuthUserResponse when user not found in database nor
     * in session.
     *
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByOAuthUserResponseWhenNoUserFound()
    {
        $user = new User([ 'id' => 1]);

        $resource = $this->getMockBuilder('Resource')
            ->setMethods([ 'getName' ])
            ->getMock();

        $response = $this->getMockBuilder('\HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMockForAbstractClass();

        $resource->expects($this->any())->method('getName')->willReturn('norf');
        $response->expects($this->any())->method('getResourceOwner')->willReturn($resource);
        $response->expects($this->any())->method('getUserName')->willReturn('glorp');

        $this->repository->expects($this->at(0))->method('findOneBy')
            ->with('norf_id = "glorp"')->will($this->throwException(new \Exception()));

        $this->session->expects($this->once())->method('get')->with('user')->willReturn(null);

        $this->provider->loadUserByOAuthUserResponse($response);
    }

    /**
     * Tests loadUserByOAuthUserResponse when user not found but accounts can be
     * linked.
     */
    public function testLoadUserByOAuthUserResponseWhenLinking()
    {
        $user = new User([ 'id' => 1]);

        $resource = $this->getMockBuilder('Resource')
            ->setMethods([ 'getName' ])
            ->getMock();

        $response = $this->getMockBuilder('\HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMockForAbstractClass();

        $resource->expects($this->any())->method('getName')->willReturn('norf');
        $response->expects($this->any())->method('getResourceOwner')->willReturn($resource);
        $response->expects($this->any())->method('getUserName')->willReturn('glorp');
        $response->expects($this->any())->method('getRealName')->willReturn('Thud');
        $response->expects($this->any())->method('getEmail')->willReturn('corge@quux.com');
        $response->expects($this->any())->method('getAccessToken')->willReturn('1234');

        $this->repository->expects($this->at(0))->method('findOneBy')
            ->with('norf_id = "glorp"')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->at(1))->method('find')
            ->with('1')->willReturn($user);

        $this->session->expects($this->once())->method('get')->with('user')->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByOAuthUserResponse($response));
        $this->assertEquals('glorp', $user->norf_id);
        $this->assertEquals('corge@quux.com', $user->norf_email);
    }

    /**
     * Tests loadUserByOAuthUserResponse when user not found and account linking
     * fails.
     *
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByOAuthUserResponseWhenLinkingFails()
    {
        $user = new User([ 'id' => 1]);

        $resource = $this->getMockBuilder('Resource')
            ->setMethods([ 'getName' ])
            ->getMock();

        $response = $this->getMockBuilder('\HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface')
            ->disableOriginalConstructor()
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMockForAbstractClass();

        $resource->expects($this->any())->method('getName')->willReturn('norf');
        $response->expects($this->any())->method('getResourceOwner')->willReturn($resource);
        $response->expects($this->any())->method('getUserName')->willReturn('glorp');
        $response->expects($this->any())->method('getRealName')->willReturn('Thud');
        $response->expects($this->any())->method('getEmail')->willReturn('corge@quux.com');
        $response->expects($this->any())->method('getAccessToken')->willReturn('1234');

        $this->repository->expects($this->at(0))->method('findOneBy')
            ->with('norf_id = "glorp"')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->at(1))->method('find')
            ->with('1')->will($this->throwException(new \Exception()));

        $this->session->expects($this->once())->method('get')->with('user')->willReturn($user);

        $this->provider->loadUserByOAuthUserResponse($response);
    }


    /**
     * Tests supportClass.
     */
    public function testSupportsClass()
    {
        $this->assertFalse($this->provider->supportsClass('Bar'));
        $this->assertTrue($this->provider->supportsClass('User'));
    }
}
