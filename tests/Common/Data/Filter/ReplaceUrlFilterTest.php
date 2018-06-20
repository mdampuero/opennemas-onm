<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Filter;

use Common\Data\Filter\ReplaceUrlFilter;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for ReplaceUrlFilter class.
 */
class ReplaceUrlFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance();

        $this->str = '<p>Taciti sociosqu ad litora torquent per
            conubia nostra, per inceptos himenaeos. Nulla lectus sem, tristique
            sed, semper in, <a href="waldo">hendrerit</a> non, sem. Vivamus
            dignissim massa in ipsum. Morbi fringilla ullamcorper ligula. Nunc
            turpis. Mauris vitae sapien. Nunc luctus bibendum velit.</p>';

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('CoreLoader')
            ->setMethods([ 'getInstance', 'loadInstanceFromInternalName' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->redirector = $this->getMockBuilder('Redirector')
            ->setMethods([ 'getTranslation' ])
            ->getMock();

        $this->ug = $this->getMockBuilder('UrlGeneratorHelper')
            ->setMethods([ 'generate', 'setInstance' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->loader->expects($this->any())->method('getInstance')
            ->willReturn($this->instance);
    }

    /**
     * Returns a mock service basing on name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.url_generator':
                return $this->ug;

            case 'core.instance':
                return $this->instance;

            case 'core.loader':
                return $this->loader;

            case 'core.redirector':
                return $this->redirector;

            case 'entity_repository':
                return $this->repository;

            default:
                return null;
        }
    }

    /**
     * Tests filter when pattern provides an slug.
     */
    public function testFilterById()
    {
        $filter = $this->getMockBuilder('Common\Data\Filter\ReplaceUrlFilter')
            ->setMethods([ 'getTranslation' ])
            ->setConstructorArgs([
                $this->container,
                [ 'pattern' => '/"(?<id>waldo)"/' ]
            ])->getMock();

        $filter->expects($this->once())->method('getTranslation')
            ->with('waldo', false)
            ->willReturn([ 'pk_content' => 1, 'type' => 'bar']);
        $this->ug->expects($this->once())->method('setInstance')
            ->with($this->instance)->willReturn($this->ug);
        $this->ug->expects($this->once())->method('generate')
            ->with('norf', [ 'absolute' => false ])
            ->willReturn('/foobar/waldo');
        $this->repository->expects($this->once())->method('find')
            ->with('Bar', 1)->willReturn('norf');

        $this->assertEquals(
            str_replace('"waldo"', '"/foobar/waldo"', $this->str),
            $filter->filter($this->str)
        );
    }

    /**
     * Tests filter when pattern provides an slug.
     */
    public function testFilterBySlug()
    {
        $filter = $this->getMockBuilder('Common\Data\Filter\ReplaceUrlFilter')
            ->setMethods([ 'getTranslation' ])
            ->setConstructorArgs([
                $this->container,
                [ 'pattern' => '/"(?<slug>waldo)"/' ]
            ])->getMock();

        $filter->expects($this->once())->method('getTranslation')
            ->with('waldo', true)
            ->willReturn([ 'pk_content' => 1, 'type' => 'bar']);
        $this->ug->expects($this->once())->method('setInstance')
            ->with($this->instance)->willReturn($this->ug);
        $this->ug->expects($this->once())->method('generate')
            ->with('norf', [ 'absolute' => false ])
            ->willReturn('/foobar/waldo');
        $this->repository->expects($this->once())->method('find')
            ->with('Bar', 1)->willReturn('norf');

        $this->assertEquals(
            str_replace('"waldo"', '"/foobar/waldo"', $this->str),
            $filter->filter($this->str)
        );
    }

    /**
     * Tests filter when pattern provides an slug.
     */
    public function testFilterWhenNoContent()
    {
        $filter = $this->getMockBuilder('Common\Data\Filter\ReplaceUrlFilter')
            ->setMethods([ 'getTranslation' ])
            ->setConstructorArgs([
                $this->container,
                [ 'pattern' => '/"(?<id>waldo)"/' ]
            ])->getMock();

        $filter->expects($this->once())->method('getTranslation')
            ->with('waldo', false)
            ->willReturn([ 'pk_content' => 1, 'type' => 'bar']);
        $this->repository->expects($this->once())->method('find')
            ->with('Bar', 1)->willReturn(null);

        $this->assertEquals($this->str, $filter->filter($this->str));
    }

    /**
     * Tests filter when no matches found.
     */
    public function testFilterWhenNoMatches()
    {
        $filter = $this->getMockBuilder('Common\Data\Filter\ReplaceUrlFilter')
            ->setMethods([ 'getTranslation' ])
            ->setConstructorArgs([
                $this->container,
                [ 'pattern' => '/(?<slug>grault)/' ]
            ])->getMock();

        $this->assertEquals($this->str, $filter->filter($this->str));
    }

    /**
     * Tests filter when no translation found.
     */
    public function testFilterWhenNoTranslation()
    {
        $filter = $this->getMockBuilder('Common\Data\Filter\ReplaceUrlFilter')
            ->setMethods([ 'getTranslation' ])
            ->setConstructorArgs([
                $this->container,
                [ 'pattern' => '/(?<slug>hendrerit)/' ]
            ])->getMock();

        $filter->expects($this->once())->method('getTranslation')
            ->with('hendrerit', true)->willReturn(null);

        $this->assertEquals($this->str, $filter->filter($this->str));
    }

    /**
     * Tests getTranslation when id provided for a list of instances
     */
    public function testGetTranslationWhenId()
    {
        $translation = [ 'pk_content' => 1, 'type' => 'flob' ];

        $filter = new ReplaceUrlFilter($this->container, [
            'instances' => [ 'wubble', 'grault' ],
            'pattern'   => '/"(?<id>waldo)"/'
        ]);

        $method = new \ReflectionMethod($filter, 'getTranslation');
        $method->setAccessible(true);

        $this->loader->expects($this->at(0))->method('loadInstanceFromInternalName')
            ->with('wubble');
        $this->loader->expects($this->at(1))->method('loadInstanceFromInternalName')
            ->with('grault');
        $this->redirector->expects($this->at(0))->method('getTranslation')
            ->with(null, null, 'waldo')->willReturn(null);
        $this->redirector->expects($this->at(1))->method('getTranslation')
            ->with(null, null, 'waldo')->willReturn($translation);

        $this->assertEquals($translation, $method->invokeArgs($filter, [ 'waldo', false ]));
    }

    /**
     * Tests getTranslation when no translation found for multiple instnaces
     */
    public function testGetTranslationWhenNoTranslationFound()
    {
        $filter = new ReplaceUrlFilter($this->container, [
            'instances' => [ 'wubble', 'grault' ],
            'pattern'   => '/"(?<id>waldo)"/'
        ]);

        $method = new \ReflectionMethod($filter, 'getTranslation');
        $method->setAccessible(true);

        $this->loader->expects($this->at(0))->method('loadInstanceFromInternalName')
            ->with('wubble');
        $this->loader->expects($this->at(1))->method('loadInstanceFromInternalName')
            ->with('grault');
        $this->redirector->expects($this->exactly(2))->method('getTranslation')
            ->with(null, null, 'waldo')->willReturn(null);

        $this->assertEmpty($method->invokeArgs($filter, [ 'waldo', false ]));
    }

    /**
     * Tests getTranslation when slug provided for the current instance.
     */
    public function testGetTranslationWhenSlug()
    {
        $translation = [ 'pk_content' => 1, 'type' => 'flob' ];

        $filter = new ReplaceUrlFilter($this->container, [
            'pattern' => '/"(?<slug>waldo)"/'
        ]);

        $method = new \ReflectionMethod($filter, 'getTranslation');
        $method->setAccessible(true);

        $this->redirector->expects($this->once())->method('getTranslation')
            ->with('waldo', null, null)->willReturn($translation);

        $this->assertEquals($translation, $method->invokeArgs($filter, [ 'waldo', true ]));
    }
}
