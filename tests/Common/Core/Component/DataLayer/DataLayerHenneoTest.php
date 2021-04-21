<?php

namespace Tests\Common\Core\Component\DataLayer;

use Common\Core\Component\DataLayer\DataLayerHenneo;

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
            ->willReturn([]);

        $dlh = new DataLayerHenneo($this->container);
        $this->assertNull($dlh->getDataLayer());
    }

    /**
     * Tests getDataLayer when not empty settings.
     */
    public function testGetDataLayer()
    {
        $settings = [
            [ 'key' => 'extension',  'value' => 'extension'],
            [ 'key' => 'date',       'value' => 'publicationDate' ],
            [ 'key' => 'updateDate', 'value' => 'updateDate'],
            [ 'key' => 'format',     'value' => 'format']
        ];

        $result = [
            'extension'  => 'blogpost',
            'date'       => '20210213',
            'updateDate' => '20210213',
            'format'     => 'web'
        ];

        $this->dataset->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn($settings);

        $this->extractor->expects($this->at(0))->method('get')
            ->with('extension')
            ->willReturn('blog');

        $this->extractor->expects($this->at(1))->method('get')
            ->with('publicationDate')
            ->willReturn('2021-02-13 00:00:00');

        $this->extractor->expects($this->at(2))->method('get')
            ->with('updateDate')
            ->willReturn('2021-02-13 00:00:00');

        $this->extractor->expects($this->at(3))->method('get')
            ->with('format')
            ->willReturn('html');

        $this->extractor->expects($this->at(4))->method('get')
            ->with('contentId')
            ->willReturn(1);

        $dlh = new DataLayerHenneo($this->container);
        $this->assertEquals($result, $dlh->getDataLayer());
    }

    /**
     * Tests getDataLayer when content frontpage.
     */
    public function testGetDataLayerWhenContentFrontpage()
    {
        $settings = [
            [ 'key' => 'extension',  'value' => 'extension']
        ];

        $this->dataset->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn($settings);

        $this->extractor->expects($this->at(0))->method('get')
            ->with('extension')
            ->willReturn('blog');

        $result = [ 'extension' => 'subhome' ];

        $dlh = new DataLayerHenneo($this->container);
        $this->assertEquals($result, $dlh->getDataLayer());
    }

    /**
     * Tests getDataLayer when manual frontpage.
     */
    public function testGetDataLayerWhenManualFrontpage()
    {
        $settings = [
            [ 'key' => 'extension', 'value' => 'extension'],
        ];

        $result = [ 'extension' => 'subhome' ];

        $this->dataset->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn($settings);

        $this->extractor->expects($this->at(0))->method('get')
            ->with('extension')
            ->willReturn('frontpages');

        $this->extractor->expects($this->at(1))->method('get')
            ->with('categoryId')
            ->willReturn(1);

        $dlh = new DataLayerHenneo($this->container);
        $this->assertEquals($result, $dlh->getDataLayer());
    }
}
