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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyUrlTest extends KernelTestCase
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

        $this->generator = $this->getMockBuilder('UrlGenerator')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('L10nRouteHelper')
            ->setMethods([ 'localizeUrl' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

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
        if ($name === 'router') {
            return $this->router;
        }

        if ($name === 'core.helper.url_generator') {
            return $this->generator;
        }

        if ($name === 'core.helper.l10n_route') {
            return $this->helper;
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
     * Tests smarty_function_get_url when item has an external link.
     */
    public function testGetUrlWhenExternalLink()
    {
        $item = json_decode(json_encode([ 'id' => '1' ]));

        $item->params = [ 'bodyLink' => 'baz' ];

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_redirect_external_link', [ 'to' => 'baz' ])
            ->willReturn('/redirect?to=baz');

        $this->assertEquals(
            '/redirect?to=baz" target="_blank',
            smarty_function_get_url([ 'item' => $item ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_get_url when item has no external link.
     */
    public function testGetUrlWhenNoExternal()
    {
        $item = json_decode(json_encode([ 'id' => '1' ]));

        $this->generator->expects($this->once())->method('generate')
            ->with($item, [ 'absolute' => true ])
            ->willReturn('http://grault.com/glorp/1');

        $this->helper->expects($this->once())->method('localizeUrl')
            ->with('http://grault.com/glorp/1', '', true)
            ->willReturn('http://grault.com/es/glorp/1');

        $this->assertEquals(
            'http://grault.com/es/glorp/1',
            smarty_function_get_url([
                'item'     => json_decode(json_encode([ 'id' => '1' ])),
                'absolute' => true
            ], $this->smarty)
        );
    }
}
