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

/**
 * Defines test cases for OAuthUserProvider class.
 */
class OAuthUserProviderTest extends \PHPUnit\Framework\TestCase
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

        $this->token = $this->getMockBuilder('Token')
            ->setMethods([ 'getUser' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('TokenStorage')
            ->setMethods([ 'getToken' ])
            ->getMock();

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->response->expects($this->any())->method('getResourceOwner')
            ->willReturn($this->resource);

        $this->provider = new OAuthUserProvider($this->em, $this->ts, [ 'foo' ]);
    }

    /**
     * Test loadUserByOAuthUserResponse when the user has already linked
     * accounts.
     */
    public function testLoadUserByOAuthUserResponseWhenLoginWithOAuth()
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
    public function testLoadUserByOAuthUserResponseWhenLoginTemporarilyWithOAuth()
    {
        $this->response->expects($this->exactly(2))->method('getResourceOwner')->willReturn($this->resource);
        $this->resource->expects($this->exactly(2))->method('getName')->willReturn('wibble');
        $this->response->expects($this->exactly(2))->method('getRealName')->willReturn('Qux Flob');
        $this->response->expects($this->exactly(3))->method('getEmail')->willReturn('mumble@corge.plugh');
        $this->response->expects($this->exactly(2))->method('getUsername')->willReturn('1234');

        $this->repository->expects($this->any())->method('findOneBy')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->any())->method('find')->will($this->throwException(new \Exception()));

        $this->ts->expects($this->once())->method('getToken')->willReturn(null);

        $user = $this->provider->loadUserByOAuthUserResponse($this->response);

        $this->assertEquals('1234', $user->wibble_id);
        $this->assertEquals('Qux Flob', $user->wibble_realname);
        $this->assertEquals('mumble@corge.plugh', $user->email);
        $this->assertEquals('mumble@corge.plugh', $user->wibble_email);
    }

    /**
     * Test loadUserByOAuthUserResponse when the user is linking his accounts.
     */
    public function testLoadUserByOAuthUserResponseWhenConnectingAccounts()
    {
        $this->resource = $this->getMockBuilder('ResourceOwner')
            ->setMethods([ 'getName' ])
            ->getMock();

        $this->response = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse')
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMock();

        $user = new User([ 'id' => 1, 'email' => 'grault@grault.quux' ]);

        $this->resource->expects($this->exactly(2))->method('getName')->willReturn('wibble');

        $this->response->expects($this->exactly(2))->method('getUsername')->willReturn('1234');
        $this->response->expects($this->once())->method('getRealname')->willReturn('Qux Flob');
        $this->response->expects($this->once())->method('getEmail')->willReturn('garply@glork.com');
        $this->response->expects($this->exactly(2))->method('getResourceOwner')->willReturn($this->resource);

        $this->repository->expects($this->any())->method('findOneBy')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->any())->method('find')->willReturn($user);

        $this->ts->expects($this->once())->method('getToken')->willReturn($this->token);
        $this->token->expects($this->once())->method('getUser')->willReturn($user);

        $this->assertEquals($user, $this->provider->loadUserByOAuthUserResponse($this->response));
        $this->assertEquals('Qux Flob', $user->wibble_realname);
        $this->assertEquals('garply@glork.com', $user->wibble_email);
    }

    /**
     * Test loadUserByOAuthUserResponse when no user found basing on the
     * response from resource.
     */
    public function testLoadUserByOAuthUserResponseWhenAccountConnectedToAnotherUser()
    {
        $userInDatabase = new User([
            'id'       => '2',
            'username' => 'wubble@frog.wibble',
            'email'    => 'wubble@frog.wibble',
        ]);

        $userInSession = new User([
            'id'       => '1',
            'username' => 'fred@bar.norf',
            'email'    => 'fred@bar.norf',
        ]);

        $this->repository->expects($this->any())->method('findOneBy')
            ->willReturn($userInDatabase);
        $this->ts->expects($this->once())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->once())->method('getUser')
            ->willReturn($userInSession);

        $this->assertEquals(
            $userInSession,
            $this->provider->loadUserByOAuthUserResponse($this->response)
        );
    }

    /**
     * Test loadUserByOAuthUserResponse when no user found basing on the
     * response from resource.
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByOAuthUserResponseWhenErrorWhileConnecting()
    {
        $this->resource = $this->getMockBuilder('ResourceOwner')
            ->setMethods([ 'getName' ])
            ->getMock();

        $this->response = $this->getMockBuilder('HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse')
            ->setMethods([ 'getAccessToken', 'getEmail', 'getRealName', 'getResourceOwner', 'getUsername' ])
            ->getMock();

        $user = new User([ 'id' => 1, 'email' => 'grault@grault.quux' ]);

        $this->resource->expects($this->exactly(2))->method('getName')->willReturn('wibble');

        $this->response->expects($this->exactly(2))->method('getUsername')->willReturn('1234');
        $this->response->expects($this->once())->method('getRealname')->willReturn('Qux Flob');
        $this->response->expects($this->once())->method('getEmail')->willReturn('garply@glork.com');
        $this->response->expects($this->exactly(2))->method('getResourceOwner')->willReturn($this->resource);

        $this->repository->expects($this->any())->method('findOneBy')->will($this->throwException(new \Exception()));
        $this->repository->expects($this->any())->method('find')->willReturn($user);

        $this->ts->expects($this->once())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->once())->method('getUser')
            ->willReturn($user);
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception()));

        $this->provider->loadUserByOAuthUserResponse($this->response);
    }

    /**
     * Tests supportClass.
     */
    public function testSupportsClass()
    {
        $this->assertTrue($this->provider->supportsClass('User'));
    }
}
