<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Translator\Google;

use Common\Core\Component\Translator\Google\GoogleTranslator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for GoogleTranslator class.
 */
class GoogleTranslatorTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->client = $this->getMockBuilder('Client')
            ->setMethods([ 'get' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->response = $this->getMockBuilder('Response')
            ->setMethods([ 'getBody' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->translator = $this->getMockBuilder('Common\Core\Component\Translator\Google\GoogleTranslator')
            ->setConstructorArgs([
                'foo',
                'fred',
                [ 'key' => 'barfooglork' ]
            ])
            ->setMethods([ 'getClient' ])
            ->getMock();

        $this->translator->expects($this->any())->method('getClient')->willReturn($this->client);
    }

    /**
     * Tests getRequiredParameters.
     */
    public function testGetRequiredParameters()
    {
        $this->assertEquals([ 'key' => _('API key') ], $this->translator->getRequiredParameters());
    }

    /**
     * Tests translate.
     */
    public function testTranslate()
    {
        $this->client->expects($this->once())->method('get')->willReturn($this->response)
            ->with('https://translation.googleapis.com/language/translate/v2?format=html&key=barfooglork&q=foobar&source=foo&target=fred');
        $this->response->expects($this->once())->method('getBody')->willReturn(json_encode([
            'data' => [ 'translations' => [ [ 'translatedText' => 'fubar' ] ] ]
        ]));

        $this->assertEmpty($this->translator->translate(''));
        $this->assertEmpty($this->translator->translate(null));
        $this->assertEquals('fubar', $this->translator->translate('foobar'));
    }

    /**
     * Tests translate.
     */
    public function testTranslateWithEmptyResponse()
    {
        $this->client->expects($this->once())->method('get')->willReturn($this->response);
        $this->response->expects($this->once())->method('getBody')->willReturn(json_encode([
            'data' => [ 'translations' => [] ]
        ]));

        $this->assertEmpty($this->translator->translate('foobar'));
    }



    /**
     * Tests translate with invalid translator configuration.
     *
     * @expectedException \RuntimeException
     */
    public function testTranslateWithInvalidConfiguration()
    {
        $this->translator->from = null;

        $this->translator->translate('bar');
    }

    /**
     * Tests getClient for a new and a existing client.
     */
    public function testGetClient()
    {
        $translator = new GoogleTranslator();
        $method = new \ReflectionMethod($translator, 'getClient');
        $method->setAccessible(true);

        $c1 = $method->invokeArgs($translator, []);

        $this->assertInstanceOf('GuzzleHttp\Client', $c1);
        $this->assertEquals($c1, $method->invokeArgs($translator, []));
    }
}
