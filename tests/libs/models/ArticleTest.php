<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests;

use Common\Data\Core\FilterManager;
use \Article;

/**
 * Defines test cases for Article class.
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->fm = new FilterManager($this->container);

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->instance->expects($this->any())->method('hasMultilanguage')
            ->willReturn(false);
        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->article = new Article();
    }

    /**
     * Returns a mock basing on the service requested to ServiceContainer mock.
     *
     * @param string $name The service name.
     *
     * @return object The mock object.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'data.manager.filter':
                return $this->fm;
        }

        return null;
    }

    /**
     * Test __get.
     */
    public function testGet()
    {
        $this->article->category_name     = 'foobar';
        $this->article->content_type_name = 'article';
        $this->article->created           = '2018-01-01 10:10:10';
        $this->article->id                = 1;
        $this->article->title             = 'wibble';

        $this->assertEquals('article', $this->article->content_type_name);
        $this->assertEquals('wibble', $this->article->title);
        $this->assertTrue(empty($this->article->pretitle));
        $this->assertTrue(empty($this->article->subtitle));

        $this->article->subtitle = 'gorp';

        $this->assertFalse(empty($this->article->pretitle));
        $this->assertEquals('gorp', $this->article->subtitle);
        $this->assertEquals('gorp', $this->article->pretitle);
    }
}
