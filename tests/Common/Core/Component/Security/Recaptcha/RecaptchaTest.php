<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Security\Recaptcha;

use Common\Core\Component\Security\Recaptcha\Recaptcha;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecaptchaTest extends KernelTestCase
{
    public function setUp()
    {
        $this->baseRecaptcha = $this->getMockBuilder('\ReCaptcha\ReCaptcha')
            ->disableOriginalConstructor()
            ->setMethods([ 'isSuccess', 'verify' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('SettingDataSet')
            ->setmethods([ 'get' ])
            ->getMock();

        $this->ormManager = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->recaptcha = new Recaptcha($this->container);
    }

    /**
     * Tests configure for backend when key is provided.
     */
    public function testConfigureForBackend()
    {
        $this->container->expects($this->at(0))->method('getParameter')
            ->with('api.recaptcha.site_key')->willReturn('fred');
        $this->container->expects($this->at(1))->method('getParameter')
            ->with('api.recaptcha.secret_key')->willReturn('fred');

        $this->recaptcha->configureForBackend();
    }

    /**
     * Tests configureForBackend when no key provided.
     *
     * @expectedException \RuntimeException
     */
    public function testConfigureForBackendWithNoKey()
    {
        $this->recaptcha->configureForBackend();
    }

    /**
     * Tests configure for frontend when key is provided.
     */
    public function testConfigureForFrontend()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->with('orm.manager')
            ->willReturn($this->ormManager);

        $this->ormManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('get')
            ->willReturn([ 'private_key' => 'wobble', 'public_key' => 'baz' ]);

        $this->recaptcha->configureForFrontend();
    }

    /**
     * Tests configureForFrontend when no key provided.
     *
     * @expectedException \RuntimeException
     */
    public function testConfigureForFrontendWithNoKey()
    {
        $this->container->expects($this->any())
            ->method('get')
            ->willReturn($this->ormManager);

        $this->ormManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('get')
            ->willReturn([]);

        $this->recaptcha->configureForFrontend();
    }

    /**
     * Tests getHtml when Recaptcha service is configured properly.
     */
    public function testGetHtml()
    {
        $locale = $this->getMockBuilder('Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLocaleShort' ])
            ->getMock();

        $locale->expects($this->any())->method('getLocaleShort')->willReturn('en');

        $this->container->expects($this->any())
            ->method('get')
            ->with('core.locale')
            ->willReturn($locale);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, $this->baseRecaptcha);

        $property = new \ReflectionProperty($this->recaptcha, 'siteKey');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, 'bar');

        $html = $this->recaptcha->getHtml();

        $this->assertContains('hl=en', $html);
        $this->assertContains('data-sitekey="bar"', $html);
    }

    /**
     * Tests getHtml when Recaptcha service is not configured properly.
     */
    public function testGetHtmlWhenNoRecaptcha()
    {
        $this->assertEquals('ReCaptcha service is not configured', $this->recaptcha->getHtml());
    }


    /**
     * Tests isValid for valid and invalid recaptcha codes.
     */
    public function testIsValid()
    {
        $this->baseRecaptcha->expects($this->any())->method('verify')->willReturn($this->baseRecaptcha);
        $this->baseRecaptcha->expects($this->at(1))->method('isSuccess')->willReturn(true);
        $this->baseRecaptcha->expects($this->at(3))->method('isSuccess')->willReturn(false);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, $this->baseRecaptcha);

        $this->assertTrue($this->recaptcha->isValid('norf', '127.0.0.1'));
        $this->assertFalse($this->recaptcha->isValid('grault', '127.0.0.1'));
    }

    /**
     * Tests isValid when recaptcha service is not configured properly.
     *
     * @expectedException \RuntimeException
     */
    public function testIsValidWhenNoRecaptcha()
    {
        $this->recaptcha->isValid('frog', '127.0.0.1');
    }
}
