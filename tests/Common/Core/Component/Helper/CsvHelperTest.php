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
        $this->writer = $this->getMockBuilder('Writer')
            ->setMethods([ 'insertAll', '__toString' ])
            ->getMock();

        $this->helper = new CsvHelper();
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

        $this->assertEquals($data, $method->invokeArgs($this->helper, [ $data ]));
    }

    /**
     * Tests parse when contents are not valid.
     */
    public function testParseWhenNoValidContents()
    {
        $data = 'waldo';

        $method = new \ReflectionMethod($this->helper, 'parse');
        $method->setAccessible(true);

        $this->assertEquals($data, $method->invokeArgs($this->helper, [ $data ]));
    }

    /**
     * Tests parse when contents are not serializable.
     */
    public function testParseWhenSerializable()
    {
        $data = [ new \Content() ];
        $date = new \Datetime('2010-01-01 00:00:00');

        $data[0]->pk_content     = 1;
        $data[0]->title          = 'waldo';
        $data[0]->created        = $date;
        $data[0]->changed        = $date;
        $data[0]->starttime      = $date;
        $data[0]->content_status = 1;

        $method = new \ReflectionMethod($this->helper, 'parse');
        $method->setAccessible(true);

        $this->assertEquals([ [
            'pk_content'     => 1,
            'title'          => 'waldo',
            'description'    => '',
            'created'        => '2010-01-01 00:00:00',
            'changed'        => '2010-01-01 00:00:00',
            'starttime'      => '2010-01-01 00:00:00',
            'content_status' => 1,
            'body'           => '',
            'pretitle'       => null
        ] ], $method->invokeArgs($this->helper, [ $data ]));
    }
}
