<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Security\Authentication;

use Common\ORM\Entity\User;
use Common\Core\Component\Exception\Security\InvalidRecaptchaException;
use Common\Core\Component\Security\Authentication\Authentication;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;

/**
 * Defines test cases for class class.
 */
class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->attributes = $this->getMockBuilder('Attributes')
            ->setMethods([ 'has', 'get' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->recaptcha = $this->getMockBuilder('Recaptcha')
            ->setMethods([ 'configureFromParameters', 'configureFromSettings', 'getHtml', 'isValid' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->getMock();

        $this->rs = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->tm = $this->getMockBuilder('TokenManager')
            ->setMethods([ 'getToken', 'isTokenValid' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('TokenStorage')
            ->setMethods([ 'setToken' ])
            ->getMock();

        $this->request->attributes = $this->attributes;

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->rs->expects($this->any())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->auth = new Authentication($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.recaptcha':
                return $this->recaptcha;

            case 'core.user':
                return 'flob';

            case 'request_stack':
                return $this->rs;

            case 'security.csrf.token_manager':
                return $this->tm;

            case 'security.token_storage':
                return $this->ts;

            case 'session':
                return $this->session;

            default:
                return null;
        }
    }

    /**
     * Tests addError.
     */
    public function testAddError()
    {
        $this->session->expects($this->once())->method('set')
            ->with(Security::AUTHENTICATION_ERROR, 'grault');

        $this->auth->addError('grault');
    }

    /**
     * Tests authenticate.
     */
    public function testAuthenticate()
    {
        $user = new User();

        $this->ts->expects($this->once())->method('setToken');
        $this->session->expects($this->exactly(2))->method('set');

        $this->auth->authenticate($user);
    }

    /**
     * Tests checkCsrfToken when token is invalid.
     */
    public function testCheckCsrfTokenWhenInvalid()
    {
        $this->tm->expects($this->once())->method('isTokenValid')
            ->willReturn(false);
        $this->session->expects($this->once())->method('set');

        $this->assertFalse($this->auth->checkCsrfToken(1234, 'mumble'));
    }

    /**
     * Tests checkCsrfToken when token is valid.
     */
    public function testCheckCsrfTokenWhenValid()
    {
        $this->tm->expects($this->once())->method('isTokenValid')
            ->willReturn(true);
        $this->session->expects($this->exactly(0))->method('set');


        $this->assertTrue($this->auth->checkCsrfToken(1234, 'mumble'));
    }

    /**
     * Tests checkRecaptcha for frontend URL.
     */
    public function testCheckRecaptchaForFrontend()
    {
        $this->recaptcha->expects($this->once())->method('configureFromSettings')
            ->willReturn($this->recaptcha);
        $this->recaptcha->expects($this->once())->method('isValid')
            ->with('foo', '198.165.167.18')->willReturn(true);

        $this->assertTrue($this->auth->checkRecaptcha(
            'foo',
            '198.165.167.18',
            'http://garply.wobble'
        ));
    }

    /**
     * Tests checkRecaptcha when token is invalid.
     */
    public function testCheckRecaptchaWhenInvalid()
    {
        $this->recaptcha->expects($this->once())->method('configureFromParameters')
            ->willReturn($this->recaptcha);
        $this->recaptcha->expects($this->once())->method('isValid')
            ->with('foo', '198.165.167.18')->willReturn(false);

        $this->session->expects($this->once())->method('set');

        $this->assertFalse($this->auth->checkRecaptcha('foo', '198.165.167.18'));
    }

    /**
     * Tests checkRecaptcha when token is valid.
     */
    public function testCheckRecaptchaWhenValid()
    {
        $this->recaptcha->expects($this->once())->method('configureFromParameters')
            ->willReturn($this->recaptcha);
        $this->recaptcha->expects($this->once())->method('isValid')
            ->with('foo', '198.165.167.18')->willReturn(true);

        $this->assertTrue($this->auth->checkRecaptcha('foo', '198.165.167.18'));
    }

    /**
     * Tests failure.
     */
    public function testFailure()
    {
        $this->session->expects($this->once())->method('get')
            ->with('failed_login_attempts')->willReturn(1);
        $this->session->expects($this->once())->method('set')
            ->with('failed_login_attempts', 2);

        $this->auth->failure();
    }

    /**
     * Tests getCsrfToken.
     */
    public function testGetCsrfToken()
    {
        $this->tm->expects($this->once())->method('getToken')
            ->willReturn('fubar');

        $this->assertEquals('fubar', $this->auth->getCsrfToken());
    }

    /**
     * Tests getError when no error.
     */
    public function testGetErrorWhenNoError()
    {
        $this->assertEmpty($this->auth->getError());
    }

    /**
     * Tests getError when error in request.
     */
    public function testGetErrorWhenErrorInRequest()
    {
        $this->attributes->expects($this->any())->method('has')
            ->willReturn(true);
        $this->attributes->expects($this->any())->method('get')
            ->willReturn('garply');

        $this->assertEquals('garply', $this->auth->getError());
    }

    /**
     * Tests getError when error in session.
     */
    public function testGetErrorWhenErrorInSession()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)->willReturn('glork');

        $this->assertEquals('glork', $this->auth->getError());
    }

    /**
     * Tests getErrorMessage when no error.
     */
    public function testGetErrorMessageWhenNoError()
    {
        $this->assertEmpty($this->auth->getErrorMessage());
    }

    /**
     * Tests getErrorMessage when the error is about invalid credentials.
     */
    public function testGetErrorMessageWhenInvalidCredentials()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new BadCredentialsException());

        $this->assertRegexp('/Username.*incorrect/', $this->auth->getErrorMessage());
    }

    /**
     * Tests getErrorMessage when the error is about invalid CSRF token.
     */
    public function testGetErrorMessageWhenInvalidCsrfToken()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new InvalidCsrfTokenException());

        $this->assertRegexp('/Login token.*not valid.*/', $this->auth->getErrorMessage());
    }

    /**
     * Tests getErrorMessage when the error is about invalid CSRF token.
     */
    public function testGetErrorMessageWhenInvalidRecaptcha()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new InvalidRecaptchaException());

        $this->assertRegexp('/.*reCAPTCHA was not.*correctly.*/', $this->auth->getErrorMessage());
    }

    /**
     * Tests getErrorMessage when the error is a unknown exception.
     */
    public function testGetErrorMessageWhenException()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new \Exception('xyzzy'));

        $this->assertEquals('xyzzy', $this->auth->getErrorMessage());
    }

    /**
     * Tests getInternalErrorMessage when no error.
     */
    public function testGetInternalErrorMessageWhenNoError()
    {
        $this->assertEmpty($this->auth->getInternalErrorMessage());
    }

    /**
     * Tests getInternalErrorMessage when the error is about invalid credentials.
     */
    public function testGetInternalErrorMessageWhenInvalidCredentials()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new BadCredentialsException());

        $this->assertEquals(
            'security.authentication.failure.credentials',
            $this->auth->getInternalErrorMessage()
        );
    }

    /**
     * Tests getInternalErrorMessage when the error is about invalid CSRF token.
     */
    public function testGetInternalErrorMessageWhenInvalidCsrfToken()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new InvalidCsrfTokenException());

        $this->assertEquals(
            'security.authentication.failure.csrf',
            $this->auth->getInternalErrorMessage()
        );
    }

    /**
     * Tests getInternalErrorMessage when the error is about invalid CSRF token.
     */
    public function testGetInternalErrorMessageWhenInvalidRecaptcha()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new InvalidRecaptchaException());

        $this->assertEquals(
            'security.authentication.failure.recaptcha',
            $this->auth->getInternalErrorMessage()
        );
    }

    /**
     * Tests getInternalErrorMessage when the error is a unknown exception.
     */
    public function testGetInternalErrorMessageWhenException()
    {
        $this->session->expects($this->any())->method('get')
            ->with(Security::AUTHENTICATION_ERROR)
            ->willReturn(new \Exception('xyzzy'));

        $this->assertEquals('xyzzy', $this->auth->getInternalErrorMessage());
    }

    public function testGetRecaptchaFromParameters()
    {
        $this->recaptcha->expects($this->once())->method('configureFromParameters')
            ->willReturn($this->recaptcha);

        $this->auth->getRecaptchaFromParameters();
    }

    public function testGetRecaptchaFromSettings()
    {
        $this->recaptcha->expects($this->once())->method('configureFromSettings')
            ->willReturn($this->recaptcha);

        $this->auth->getRecaptchaFromSettings();
    }

    /**
     * Tests hasError when there is no error in service.
     */
    public function testHasErrorWhenNoError()
    {
        $this->assertFalse($this->auth->hasError());
    }

    /**
     * Tests hasError when there is an authentication error in request.
     */
    public function testHasErrorWhenErrorInRequest()
    {
        $this->attributes->expects($this->any())->method('has')
            ->willReturn(true);
        $this->attributes->expects($this->any())->method('get')
            ->willReturn('garply');

        $this->assertTrue($this->auth->hasError());
    }

    /**
     * Tests hasError when there is an authentication error in session.
     */
    public function testHasErrorWhenErrorInSession()
    {
        $this->session->expects($this->any())->method('get')
            ->willReturn('wibble');

        $this->assertTrue($this->auth->hasError());
    }

    /**
     * Tests isAuthenticated where there is no user in the service container.
     */
    public function testIsAuthenticatedWhenNoUser()
    {
        $container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $auth = new Authentication($container);

        $this->assertFalse($auth->isAuthenticated());
    }

    /**
     * Tests isAuthenticated where there is a user in the service container.
     */
    public function testIsAuthenticatedWhenUser()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.user')->willReturn('fred');

        $this->assertTrue($this->auth->isAuthenticated());
    }

    /**
     * Tests isRecaptchaRequired when it is and it is not required.
     */
    public function testIsRecaptchaRequired()
    {
        $this->session->expects($this->at(0))->method('get')
            ->with('failed_login_attempts')->willReturn(0);
        $this->session->expects($this->at(1))->method('get')
            ->with('failed_login_attempts')->willReturn(3);

        $this->assertFalse($this->auth->isRecaptchaRequired());
        $this->assertTrue($this->auth->isRecaptchaRequired());
    }

    /**
     * Tests success.
     */
    public function testSuccess()
    {
        $this->session->expects($this->at(0))->method('set')
            ->with(Security::AUTHENTICATION_ERROR, null);
        $this->session->expects($this->at(1))->method('set')
            ->with('failed_login_attempts', 0);

        $this->auth->success();
    }

    /**
     * Tests getIntention.
     */
    public function testIntention()
    {
        $method = new \ReflectionMethod($this->auth, 'getIntention');
        $method->setAccessible(true);

        $this->assertRegexp('/[0-9]{14,}/', $method->invokeArgs($this->auth, []));
    }

    /**
     * Tests isRecaptchaForFrontend when the referer is and is not a frontend
     * URL.
     */
    public function testIsRecaptchaForFrontend()
    {
        $method = new \ReflectionMethod($this->auth, 'isRecaptchaForFrontend');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->auth, [ 'http://qux.glork' ]));
        $this->assertFalse($method->invokeArgs($this->auth, [ 'http://qux.glork/admin' ]));
        $this->assertFalse($method->invokeArgs($this->auth, [ 'http://qux.glork/manager' ]));
    }
}
