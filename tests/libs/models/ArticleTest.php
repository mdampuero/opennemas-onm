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

use \Article;

/**
 * Defines test cases for Article class.
 */
class ArticleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);
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
        $this->article->content_type_name = 'article';
        $this->article->created           = '2018-01-01 10:10:10';
        $this->article->id                = 1;
        $this->article->title             = 'wibble';

        $this->fm->expects($this->at(2))->method('get')
            ->with()->willReturn('wibble');

        $this->assertEquals('article', $this->article->content_type_name);
        $this->assertEquals('wibble', $this->article->title);
        $this->assertTrue(empty($this->article->pretitle));

        $this->article->pretitle = 'flob';

        $this->fm->expects($this->at(2))->method('get')
            ->with()->willReturn('flob');

        $this->assertEquals('flob', $this->article->pretitle);
    }
}
