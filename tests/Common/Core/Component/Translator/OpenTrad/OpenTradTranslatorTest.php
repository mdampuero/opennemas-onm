<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Translator\OpenTrad;

use Common\Core\Component\Translator\OpenTrad\OpenTradTranslator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for OpenTradTranslator class.
 */
class OpenTradTranslatorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder('soapclient')
            ->setMethods([ 'call' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->translator = $this->getMockBuilder('Common\Core\Component\Translator\OpenTrad\OpenTradTranslator')
            ->setConstructorArgs([
                'bar',
                'fubar',
                [ 'url' => 'www.foobar.com', 'translator' => 'wubble' ]
            ])
            ->setMethods([ 'getClient' ])
            ->getMock();

        $this->translator->expects($this->any())->method('getClient')->willReturn($this->client);
    }

    /**
     * Tests get and set with properties and parameters.
     */
    public function testGetAndSet()
    {
        $this->assertEquals('bar', $this->translator->from);
        $this->translator->from = 'flob';
        $this->assertEquals('flob', $this->translator->from);

        $this->assertEquals('fubar', $this->translator->to);
        $this->translator->to = 'xyzzy';
        $this->assertEquals('xyzzy', $this->translator->to);

        $this->assertEquals('wubble', $this->translator->translator);
        $this->translator->translator = 'thud';
        $this->assertEquals('thud', $this->translator->translator);

        $this->assertEmpty($this->translator->frog);
        $this->assertTrue(empty($this->translator->baz));
    }

    /**
     * Tests getRequiredParameters.
     */
    public function testGetRequiredParameters()
    {
        $translator = new OpenTradTranslator('glorp', 'bar', []);

        $this->assertEquals(
            [ 'translator' => _('Translator'), 'url' => 'URL' ],
            $translator->getRequiredParameters()
        );
    }

    /**
     * Tests translate.
     */
    public function testTranslate()
    {
        $this->client->expects($this->at(0))->method('call')->with('traducir', [
            'tradutor'  => 'wubble',
            'direccion' => "bar-fubar",
            'tipo'      => 'htmlu',
            'cadea'     => 'bar frog fubar'
        ])->willReturn('glorp norf wobble');

        $this->client->expects($this->at(1))->method('call')->with('traducir', [
            'tradutor'  => 'wubble',
            'direccion' => "thud-norf",
            'tipo'      => 'htmlu',
            'cadea'     => 'bar frog fubar'
        ])->willReturn('wobble bar wibble');

        $this->assertEquals('', $this->translator->translate(null));
        $this->assertEquals('', $this->translator->translate(''));
        $this->assertEquals('glorp norf wobble', $this->translator->translate('bar frog fubar'));
        $this->assertEquals('wobble bar wibble', $this->translator->translate('bar frog fubar', 'thud', 'norf'));
    }

    /**
     * Tests translate with invalid translator configuration.
     *
     * @expectedException \RuntimeException
     */
    public function testTranslateWithInvalidConfiguration()
    {
        $this->translator->translator = null;
        $this->translator->translate('bar');
    }

    /**
     * Tests translate when client fails.
     *
     * @expectedException \Common\Core\Component\Exception\Translator\InvalidTranslationException
     */
    public function testTranslateWhenRequestFails()
    {
        $this->client->expects($this->at(0))->method('call')
            ->will($this->throwException(new \Exception()));
        $this->translator->translate('bar');
    }

    /**
     * Tests translate when client returns false.
     *
     * @expectedException \Common\Core\Component\Exception\Translator\InvalidTranslationException
     */
    public function testTranslateWithEmptyResponse()
    {
        $this->client->expects($this->at(0))->method('call')->willReturn(false);
        $this->translator->translate('bar');
    }

    /**
     * Tests translate when client returns an error string.
     *
     * @expectedException \Common\Core\Component\Exception\Translator\InvalidTranslationException
     */
    public function testTranslateWithInvalidResponse()
    {
        $this->client->expects($this->at(0))->method('call')
            ->willReturn('lt-proc: process a stream with a letter transducer');

        $this->translator->translate('bar');
    }

    /**
     * Tests getClient for a new and a existing client.
     */
    public function testGetClient()
    {
        $translator = new OpenTradTranslator('wibble', 'quux', [
            'url' => 'www.foobar.com', 'translator' => 'wubble'
        ]);

        $method = new \ReflectionMethod($translator, 'getClient');
        $method->setAccessible(true);

        $c1 = $method->invokeArgs($translator, []);

        $this->assertInstanceOf('nusoap_client', $c1);
        $this->assertEquals($c1, $method->invokeArgs($translator, []));
    }
}
