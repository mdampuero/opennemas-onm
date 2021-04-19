<?php

namespace Tests\Common\Core\Component\DataLayer;

use Common\Core\Component\DataLayer\DataLayerHenneo;
use ReflectionMethod;

/**
 * Defines test cases for henneo data layer.
 */
class DataLayerHenneoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('Opennemas\Orm\Core\DataSet')
            ->setMethods([ 'delete', 'get', 'init', 'set' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->extractor = $this->getMockBuilder('Common\Core\Component\Core\VariablesExtractor')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->dataset);

        $this->dl = new DataLayerHenneo($this->container);
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;

            case 'core.variables.extractor':
                return $this->extractor;
        }
    }

    /**
     * Tests getDataLayer when there is no settings.
     */
    public function testGetDataLayerWhenEmpty()
    {
        $this->dataset->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn(null);

        $this->assertNull($this->dl->getDataLayer());
    }

    /**
     * Tests getDataLayer when not empty settings.
     */
    public function testGetDataLayer()
    {
        $settings = [
            [ 'key' => 'author_id', 'value' => 'authorId' ],
            [ 'key' => 'author_name', 'value' => 'authorName'],
            [ 'key' => 'extension', 'value' => 'extension']
        ];

        $result = [
            'author_id'   => 1,
            'author_name' => 'Baz Glorp',
            'extension'   => 'subhome'
        ];

        $this->dataset->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn($settings);

        $this->extractor->expects($this->at(0))->method('get')
            ->with('authorId')
            ->willReturn(1);

        $this->extractor->expects($this->at(1))->method('get')
            ->with('authorName')
            ->willReturn('Baz Glorp');

        $this->extractor->expects($this->at(2))->method('get')
            ->with('extension')
            ->willReturn('frontpages');

        $this->extractor->expects($this->at(3))->method('get')
            ->with('categoryId')
            ->willReturn(1);

        $this->assertEquals($result, $this->dl->getDataLayer());
    }

    /**
     * Tests customize when performs a standard extension replacement.
     */
    public function testCustomizeWhenExtension()
    {
        $method = new ReflectionMethod(get_class($this->dl), 'customize');
        $method->setAccessible(true);

        $this->extractor->expects($this->once())->method('get')
            ->with('extension')
            ->willReturn('blog');

        $this->assertEquals(
            'blogpost',
            $method->invokeArgs($this->dl, [ [ 'key' => 'extension', 'value' => 'extension' ] ])
        );
    }

    /**
     * Tests customize when performs a format replacement.
     */
    public function testCustomizeWhenFormat()
    {
        $method = new ReflectionMethod(get_class($this->dl), 'customize');
        $method->setAccessible(true);

        $this->extractor->expects($this->once())->method('get')
            ->with('format')
            ->willReturn('html');

        $this->assertEquals(
            'web',
            $method->invokeArgs($this->dl, [ [ 'key' => 'format', 'value' => 'format' ] ])
        );
    }

    /**
     * Tests customize when performs a date replacement.
     */
    public function testCustomizeWhenDate()
    {
        $method = new ReflectionMethod(get_class($this->dl), 'customize');
        $method->setAccessible(true);

        $this->extractor->expects($this->once())->method('get')
            ->with('publicationDate')
            ->willReturn('2021-04-19 11:35:45');

        $this->assertEquals(
            '20210419',
            $method->invokeArgs($this->dl, [ [ 'key' => 'date', 'value' => 'publicationDate' ] ])
        );
    }
}
