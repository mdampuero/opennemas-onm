<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyGetUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.get_url.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->decorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $this->generator = $this->getMockBuilder('UrlGenerator')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->decorator->expects($this->any())->method('prefixUrl')
            ->will($this->returnArgument(0));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'router':
                return $this->router;

            case 'core.helper.url_generator':
                return $this->generator;

            case 'core.decorator.url':
                return $this->decorator;
        }

        return null;
    }

    /**
     * Tests smarty_function_get_url when no a valid item provided.
     */
    public function testGetUrlWhenNoItem()
    {
        $this->assertEmpty(smarty_function_get_url([], $this->smarty));
        $this->assertEmpty(smarty_function_get_url([ 'item' => 'gorp' ], $this->smarty));
        $this->assertEmpty(smarty_function_get_url([
            'item' => json_decode(json_encode([ 'id' => null ]))
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_get_url when item has no external link.
     */
    public function testGetUrlWhenNoExternal()
    {
        $item = json_decode(json_encode([ 'id' => '1' ]));

        $this->generator->expects($this->once())->method('generate')
            ->with($item, [ 'absolute' => true,'_format'  => null, 'localize' => null ])
            ->willReturn('http://grault.com/glorp/1');

        $this->assertEquals(
            'http://grault.com/glorp/1',
            smarty_function_get_url([
                'item'     => $item,
                'absolute' => true,
                '_format'  => null
            ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_get_url when item is AMP format
     */
    public function testGetUrlWhenAMP()
    {
        $item = json_decode(json_encode([
            'id'                => '1',
            'content_type_name' => 'article'
        ]));

        $this->generator->expects($this->once())->method('generate')
            ->with($item, [ 'absolute' => true, '_format' => 'amp', 'localize' => null ])
            ->willReturn('http://grault.com/glorp.amp.html');

        $this->assertEquals(
            'http://grault.com/glorp.amp.html',
            smarty_function_get_url([
                'item'     => $item,
                'amp'      => true,
                'absolute' => true
            ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_get_url when localize
     */
    public function testGetUrlWhenLocalize()
    {
        $item = json_decode(json_encode([
            'id'                => '1',
            'content_type_name' => 'article'
        ]));

        $this->generator->expects($this->once())->method('generate')
            ->with($item, [ 'absolute' => true, '_format' => null, 'localize' => 'en' ])
            ->willReturn('http://grault.com/en/glorp.amp.html');

        $this->assertEquals(
            'http://grault.com/en/glorp.amp.html',
            smarty_function_get_url([
                'item'     => $item,
                '_format'  => null,
                'absolute' => true,
                'slug'   => 'en'
            ], $this->smarty)
        );
    }
}
