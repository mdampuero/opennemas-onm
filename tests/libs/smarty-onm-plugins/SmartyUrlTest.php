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

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.url.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
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

        $this->helper->expects($this->any())->method('localizeUrl')
            ->will($this->returnArgument(0));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        if ($name === 'router') {
            return $this->router;
        }

        if ($name === 'core.helper.l10n_route') {
            return $this->helper;
        }

        return null;
    }

    /**
     * Tests smarty_function_url when router throws an exception.
     */
    public function testUrlWhenException()
    {
        $this->router->expects($this->once())->method('generate')
            ->will($this->throwException(new \Exception));

        $this->assertEquals('#not-found', smarty_function_url([
            'name' => 'waldo',
            'fred' => 'foobar'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_url when router throws an exception.
     */
    public function testUrlWhenRouteNotFound()
    {
        $this->router->expects($this->once())->method('generate')
            ->will($this->throwException(new RouteNotFoundException()));

        $this->assertEquals('#not-found-waldo', smarty_function_url([
            'name'     => 'waldo',
            'absolute' => true,
            'fred'     => 'foobar'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_url when no route name provided.
     */
    public function testUrlWithNoName()
    {
        $this->assertEmpty(smarty_function_url([
            'fred' => 'foobar'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_url when no slug required.
     */
    public function testUrlWithNoSlug()
    {
        $this->router->expects($this->once())->method('generate')
            ->with('waldo', [ 'fred' => 'foobar' ])
            ->willReturn('/waldo/foobar');

        $this->assertEquals('/waldo/foobar', smarty_function_url([
            'name' => 'waldo',
            'fred' => 'foobar'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_url when slug required and sluggable parameter
     * provided.
     */
    public function testUrlWithSlugProvided()
    {
        $this->router->expects($this->once())->method('generate')
            ->with('waldo', [ 'frog' => 'baz' ])
            ->willReturn('/waldo/baz');

        $this->assertEquals('/waldo/baz', smarty_function_url([
            'name' => 'waldo',
            'frog' => 'baz',
            'sluggable' => true,
            'slug_key' => 'frog'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_url when slug required but no sluggable parameter
     * provided.
     *
     * @covers ::smarty_function_url
     */
    public function testUrlWithSlugNotProvided()
    {
        $this->router->expects($this->once())->method('generate')
            ->with('waldo', [ 'frog' => 'baz' ]);

        $this->assertEmpty(smarty_function_url([
            'name' => 'waldo',
            'frog' => 'baz',
            'sluggable' => true,
            'slug_key' => 'flob'
        ], $this->smarty));
    }
}
