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

class RecaptchaTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->baseRecaptcha = $this->getMockBuilder('\ReCaptcha\ReCaptcha')
            ->disableOriginalConstructor()
            ->setMethods([ 'isSuccess', 'verify', 'getHostName' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods(['getCurrentRequest'])->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods(['getHost'])->getMock();

        $this->recaptchaResponse = $this->getMockBuilder('Response')
            ->setMethods(['getHostName', 'isSuccess'])->getMock();
    }

    /**
     * Tests configure for backend when key is provided.
     */
    public function testConfigureForBackend()
    {
        $this->container->expects($this->at(0))->method('getParameter')
            ->with('api.recaptcha.site_key')->willReturn('fred');
        $this->container->expects($this->at(1))->method('getParameter')
            ->with('api.recaptcha.secret_key')->willReturn('fres');

        $keys = [
            'siteKey'   => 'fred',
            'secretKey' => 'fres',
        ];

        $this->recaptcha = new Recaptcha($this->container);
        $this->recaptcha->configureFromParameters();

        $this->assertAttributeEquals($keys, 'recaptchaKeys', $this->recaptcha);
    }

    /**
     * Tests configureFromParameters when no key provided.
     *
     * @expectedException \RuntimeException
     */
    public function testConfigureForBackendWithNoKey()
    {
        $this->recaptcha = new Recaptcha($this->container);
        $this->recaptcha->configureFromParameters();
    }

    /**
     * Tests configure for frontend when key is provided.
     */
    public function testConfigureForFrontend()
    {
        $keys = [
            'siteKey'   => 'site_wooble',
            'secretKey' => 'secret_baz',
        ];

        $this->container->expects($this->any())
            ->method('get')
            ->with('orm.manager')
            ->willReturn($this->em);

        $this->em->expects($this->once())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->ds->expects($this->once())
            ->method('get')
            ->with('recaptcha')
            ->willReturn([
                'public_key' => 'site_wooble',
                'private_key' => 'secret_baz'
            ]);

        $this->recaptcha = new Recaptcha($this->container);
        $this->recaptcha->configureFromSettings();

        $this->assertAttributeEquals($keys, 'recaptchaKeys', $this->recaptcha);
    }

    /**
     * Tests configureFromSettings when no key provided.
     *
     * @expectedException \RuntimeException
     */
    public function testConfigureForFrontendWithNoKey()
    {
        $keys = [
            'siteKey'   => 'fred',
            'secretKey' => 'fres',
        ];

        $this->container->expects($this->once())->method('get')
            ->with('orm.manager') ->willReturn($this->em);

        $this->em->expects($this->once())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->ds->expects($this->once())->method('get')
            ->with('recaptcha')->willReturn([]);

        $this->recaptcha = new Recaptcha($this->container);
        $this->recaptcha->configureFromSettings();

        $this->assertAttributeEquals($keys, 'recaptchaKeys', $this->recaptcha);
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

        $this->container->expects($this->once())
            ->method('get')
            ->with('core.locale')
            ->willReturn($locale);

        $this->recaptcha = new Recaptcha($this->container);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, 'bar');

        $property = new \ReflectionProperty($this->recaptcha, 'recaptchaKeys');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, [
            'siteKey'   => 'fred',
            'secretKey' => 'fres',
        ]);
        $html = $this->recaptcha->getHtml();

        $this->assertContains('hl=en', $html);
        $this->assertContains('data-sitekey="fred"', $html);
    }

    /**
     * Tests getHtml when Recaptcha service is not configured properly.
     */
    public function testGetHtmlWhenNoRecaptcha()
    {
        $this->recaptcha = new Recaptcha($this->container);

        $this->assertEquals('ReCaptcha service is not configured', $this->recaptcha->getHtml());
    }

    /**
     * Tests isValid for valid and invalid recaptcha codes.
     */
    public function testIsValid()
    {
        $hostName = 'www.example.com';

        $this->request->expects($this->any())
            ->method('getHost')->willReturn($hostName);
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);
        $this->container->expects($this->any())
            ->method('get')->with('request_stack')->willReturn($this->requestStack);

        $this->recaptchaResponse->expects($this->any())->method('isSuccess')->willReturn(true);
        $this->recaptchaResponse->expects($this->any())->method('getHostName')->willReturn($hostName);
        $this->baseRecaptcha->expects($this->any())->method('verify')->willReturn($this->recaptchaResponse);

        $this->recaptcha = new Recaptcha($this->container);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, $this->baseRecaptcha);

        $this->assertTrue($this->recaptcha->isValid('norf', '127.0.0.1'));
    }

    /**
     * Tests isValid for valid and invalid recaptcha codes.
     */
    public function testIsValidWithRecaptchaFalseValue()
    {
        $hostName = 'www.example.com';

        $this->request->expects($this->any())
            ->method('getHost')->willReturn($hostName);
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);
        $this->container->expects($this->any())
            ->method('get')->with('request_stack')->willReturn($this->requestStack);

        $this->recaptchaResponse->expects($this->any())->method('isSuccess')->willReturn(false);
        $this->recaptchaResponse->expects($this->any())->method('getHostName')->willReturn($hostName);
        $this->baseRecaptcha->expects($this->any())->method('verify')->willReturn($this->recaptchaResponse);

        $this->recaptcha = new Recaptcha($this->container);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, $this->baseRecaptcha);

        $this->assertFalse($this->recaptcha->isValid('norf', '127.0.0.1'));
    }

    /**
     * Tests isValid for valid and invalid recaptcha codes.
     */
    public function testIsValidWithRecaptchaDifferentHostNames()
    {
        $hostName = 'www.example.com';

        $this->request->expects($this->any())
            ->method('getHost')->willReturn($hostName);
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);
        $this->container->expects($this->any())
            ->method('get')->with('request_stack')->willReturn($this->requestStack);

        $this->recaptchaResponse->expects($this->any())->method('isSuccess')->willReturn(true);
        $this->recaptchaResponse->expects($this->any())->method('getHostName')->willReturn('invalid.hostname.com');
        $this->baseRecaptcha->expects($this->any())->method('verify')->willReturn($this->recaptchaResponse);

        $this->recaptcha = new Recaptcha($this->container);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, $this->baseRecaptcha);

        $this->assertFalse($this->recaptcha->isValid('norf', '127.0.0.1'));
    }

    /**
     * Tests isValid when recaptcha service is not configured properly.
     *
     * @expectedException \RuntimeException
     */
    public function testIsValidWhenNoRecaptcha()
    {
        $this->recaptcha = new Recaptcha($this->container);

        $property = new \ReflectionProperty($this->recaptcha, 'recaptcha');
        $property->setAccessible(true);
        $property->setValue($this->recaptcha, null);

        $this->recaptcha->isValid('frog', '127.0.0.1');
    }
}
