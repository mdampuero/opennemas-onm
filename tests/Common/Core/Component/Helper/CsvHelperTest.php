<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\CsvHelper;

/**
 * Defines test cases for CsvHelper class.
 */
class CsvHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Cache')
            ->setMethods([ 'fetch' ])
            ->getMock();

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

        $this->writer = $this->getMockBuilder('Writer')
            ->setMethods([ 'insertAll', 'insertOne', '__toString' ])
            ->getMock();

        $this->cache->expects($this->any())->method('fetch')
            ->willReturn([]);
        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->fm->expects($this->any())->method('set')
            ->willReturn($this->fm);
        $this->fm->expects($this->any())->method('filter')
            ->willReturn($this->fm);
        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->helper = new CsvHelper();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'cache':
                return $this->cache;

            case 'data.manager.filter':
                return $this->fm;

            case 'core.instance':
                return $this->instance;

            default:
                return null;
        }
    }

    /**
     * Tests getReport when no filename provided.
     */
    public function testGetReport()
    {
        $helper = $this->getMockBuilder('Common\Core\Component\Helper\CsvHelper')
            ->setMethods([ 'getWriter' ])
            ->getMock();

        $helper->expects($this->once())->method('getWriter')
            ->willReturn($this->writer);

        $this->writer->expects($this->once())->method('insertOne');
        $this->writer->expects($this->once())->method('insertAll');
        $this->writer->expects($this->once())->method('__toString');

        $helper->getReport([]);
    }

    /**
     * Tests getWriter when it is called multiple times.
     */
    public function testGetWriter()
    {
        $method = new \ReflectionMethod($this->helper, 'getWriter');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'League\Csv\Writer',
            $method->invokeArgs($this->helper, [])
        );
    }

    /**
     * Tests parse when contents are not serializable.
     */
    public function testParseWhenNoSerializable()
    {
        $data = [ 'a', 'b', 'c' ];

        $method = new \ReflectionMethod($this->helper, 'parse');
        $method->setAccessible(true);

        $this->assertEquals([ [], $data ], $method->invokeArgs($this->helper, [ $data ]));
    }

    /**
     * Tests parse when contents are not valid.
     */
    public function testParseWhenNoValidContents()
    {
        $data = 'waldo';

        $method = new \ReflectionMethod($this->helper, 'parse');
        $method->setAccessible(true);

        $this->assertEquals([ [], $data ], $method->invokeArgs($this->helper, [ $data ]));
    }

    /**
     * Tests parse when contents are not serializable.
     */
    public function testParseWhenSerializable()
    {
        $data = [ new \Content(), new \Content() ];
        $date = new \Datetime('2010-01-01 00:00:00');

        $data[0]->pk_content     = 1;
        $data[0]->content_type_name     = 'poll';
        $data[0]->title          = 'waldo';
        $data[0]->created        = $date;
        $data[0]->changed        = $date;
        $data[0]->starttime      = $date;
        $data[0]->content_status = 1;
        $data[0]->items          = [
            [ 'pk_item' => 1, 'item' => 'flob', 'votes' => 35  ],
            [ 'pk_item' => 2, 'item' => 'corge', 'votes' => 20 ]
        ];

        $data[1]->pk_content     = 2;
        $data[1]->content_type_name     = 'poll';
        $data[1]->title          = 'gorp';
        $data[1]->created        = $date;
        $data[1]->changed        = $date;
        $data[1]->starttime      = $date;
        $data[1]->content_status = 1;
        $data[1]->items          = [
            [ 'pk_item' => 1, 'item' => 'foobar', 'votes' => 5 ],
            [ 'pk_item' => 2, 'item' => 'foo', 'votes' => 100 ],
            [ 'pk_item' => 3, 'item' => 'bar', 'votes' => 40 ]
        ];

        // body, description, title, pretitle, item0, item1
        $this->fm->expects($this->at(2))->method('get')
            ->willReturn('');
        $this->fm->expects($this->at(5))->method('get')
            ->willReturn('');
        $this->fm->expects($this->at(8))->method('get')
            ->willReturn('waldo');
        $this->fm->expects($this->at(11))->method('get')
            ->willReturn(null);
        $this->fm->expects($this->at(14))->method('get')
            ->willReturn('');
        $this->fm->expects($this->at(17))->method('get')
            ->willReturn('gorp');


        $method = new \ReflectionMethod($this->helper, 'parse');
        $method->setAccessible(true);

        list($headers, $values) = $method->invokeArgs($this->helper, [ $data ]);


        $this->assertEquals([
            'pk_content', 'pretitle', 'title', 'description', 'created',
            'changed', 'starttime', 'content_status', 'body'
        ], $headers);

        $this->assertEquals([
            [
                'pk_content'     => 1,
                'title'          => 'waldo',
                'description'    => '',
                'created'        => '2010-01-01 00:00:00',
                'changed'        => '2010-01-01 00:00:00',
                'starttime'      => '2010-01-01 00:00:00',
                'content_status' => 1,
                'body'           => '',
                'pretitle'       => null
            ],
            [
                'pk_content'     => 2,
                'title'          => 'gorp',
                'description'    => '',
                'created'        => '2010-01-01 00:00:00',
                'changed'        => '2010-01-01 00:00:00',
                'starttime'      => '2010-01-01 00:00:00',
                'content_status' => 1,
                'body'           => null,
                'pretitle'       => null
            ]
        ], $values);
    }
}
