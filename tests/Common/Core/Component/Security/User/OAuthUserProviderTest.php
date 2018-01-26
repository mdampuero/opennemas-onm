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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for OAuthUserProvider class.
 */
class OAuthUserProviderTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    protected function setUp()
    {
        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getRepository', 'persist' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('\Common\ORM\Database\Repository\BaseRepository')
            ->disableOriginalConstructor()
            ->setMethods([ 'find', 'findOneBy' ])
            ->getMock();

        $this->resource = $this->getMockBuilder('ResourceOwner')
            ->setMethods([ 'getName' ])
            ->getMock();

        $this->response = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse')
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->provider = new OAuthUserProvider($this->em, $this->session, [ 'foo' ]);
    }

    /**
     * Test loadUserByOAuthUserResponse when the user has already linked
     * accounts.
     */
    public function testLoadUserByOAuthUserResponse()
    {
        $this->resource = $this->getMockBuilder('ResourceOwner')
            ->setMethods([ 'getName' ])
            ->getMock();

        $this->response = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse')
            ->setMethods([ 'getResourceOwner', 'getUsername' ])
            ->getMock();

        $user = new User();

        $this->resource->expects($this->once())->method('getName')->willReturn('wibble');

        $this->response->expects($this->once())->method('getUsername')->willReturn('1234');
        $this->response->expects($this->once())->method('getResourceOwner')
            ->willReturn($this->resource);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('wibble_id = "1234"')
            ->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByOAuthUserResponse($this->response));
    }

    /**
     * Test loadUserByOAuthUserResponse when the user is linking his accounts.
     */
    public function testLoadUserByOAuthUserResponseWhenLinking()
    {
        $this->resource = $this->getMockBuilder('ResourceOwner')
            ->setMethods([ 'getName' ])
            ->getMock();

        $this->response = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse')
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMock();

        $user = new User([ 'id' => 1 ]);

        $this->resource->expects($this->exactly(2))->method('getName')->willReturn('wibble');

        $this->response->expects($this->exactly(2))->method('getUsername')->willReturn('1234');
        $this->response->expects($this->once())->method('getRealname')->willReturn('Qux Flob');
        $this->response->expects($this->once())->method('getEmail')->willReturn('garply@glork.com');
        $this->response->expects($this->exactly(2))->method('getResourceOwner')->willReturn($this->resource);

        $this->repository->expects($this->any())->method('findOneBy')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->any())->method('find')->willReturn($user);

        $this->session->expects($this->once())->method('get')->with('user')->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByOAuthUserResponse($this->response));
        $this->assertEquals('Qux Flob', $user->wibble_realname);
        $this->assertEquals('garply@glork.com', $user->wibble_email);
    }

    /**
     * Test loadUserByOAuthUserResponse when the user is linking his accounts.
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByOAuthUserResponseWhenNoUserInDatabase()
    {
        $user = new User([ 'id' => 1 ]);

        $this->resource->expects($this->exactly(2))->method('getName')->willReturn('wibble');

        $this->response->expects($this->exactly(2))->method('getUsername')->willReturn('1234');
        $this->response->expects($this->exactly(2))->method('getResourceOwner')->willReturn($this->resource);

        $this->repository->expects($this->any())->method('findOneBy')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->any())->method('find')->will($this->throwException(new \Exception()));

        $this->session->expects($this->once())->method('get')->with('user')->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByOAuthUserResponse($this->response));
        $this->assertEquals('Qux Flob', $user->wibble_realname);
        $this->assertEquals('garply@glork.com', $user->wibble_email);
    }

    /**
     * Test loadUserByOAuthUserResponse when no user found basing on the
     * response from resource.
     */
    public function testLoadUserByOAuthUserResponseWhenNoUserInSession()
    {
        $this->resource = $this->getMockBuilder('ResourceOwner')
            ->setMethods([ 'getName' ])
            ->getMock();

        $this->response = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse')
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMock();

        $user = new User([
            'name'          => 'Grault Thud',
            'username'      => 'fred@bar.norf',
            'email'         => 'fred@bar.norf',
            'activated'     => true,
            'type'          => 1,
            'fk_user_group' => [],
            'wibble_email'    => 'fred@bar.norf',
            'wibble_id'       => 1234,
            'wibble_realname' => 'Grault Thud',
            'wibble_token'    => 'bazflobplugh',
        ]);

        $this->response->expects($this->exactly(2))->method('getResourceOwner')->willReturn($this->resource);
        $this->resource->expects($this->exactly(2))->method('getName')->willReturn('wibble');
        $this->response->expects($this->exactly(2))->method('getUsername')->willReturn(1234);
        $this->response->expects($this->exactly(3))->method('getEmail')->willReturn('fred@bar.norf');
        $this->response->expects($this->exactly(2))->method('getRealName')->willReturn('Grault Thud');
        $this->response->expects($this->once())->method('getAccessToken')->willReturn('bazflobplugh');
        $this->repository->expects($this->any())->method('findOneBy')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->any())->method('find')->willReturn($user);

        $this->session->expects($this->once())->method('get')->with('user')->willReturn(null);

        $this->assertEquals(
            $user,
            $this->provider->loadUserByOAuthUserResponse($this->response)
        );
    }

    /**
     * Tests supportClass.
     */
    public function testSupportsClass()
    {
        $this->assertTrue($this->provider->supportsClass('User'));
    }
}
