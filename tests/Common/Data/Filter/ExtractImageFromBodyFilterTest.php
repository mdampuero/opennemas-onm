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

use Common\Data\Filter\ExtractImageFromBodyFilter;

/**
 * Defines test cases for ExtractImageFromBodyFilter class.
 */
class ExtractImageFromBodyFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Connection')
            ->setMethods([ 'fetchAssoc', 'insert' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('dbal_connection')->willReturn($this->conn);

        $this->filter = new ExtractImageFromBodyFilter($this->container);
    }

    /**
     * Tests filter when separator not found in string.
     */
    public function testFilterWhenSeparatorNotFound()
    {
        $str = '<img src="xyzzy/fubar/norf.jpg">';

        $this->assertEquals($str, $this->filter->filter($str));
    }

    /**
     * Tests filter when no matches found in string.
     */
    public function testFilterWhenNoMatches()
    {
        $str = 'grault@@@@2019-02-27 14:15:31';

        $this->assertEquals(null, $this->filter->filter($str));
    }

    /**
     * Tests filter when a match is found in string.
     */
    public function testFilterWhenMatchesFound()
    {
        $filter = $this->getMockBuilder('Common\Data\Filter\ExtractImageFromBodyFilter')
            ->setConstructorArgs([ $this->container, [
                'separator'    => '@@@@'
            ]])->setMethods([ 'importPhotos' ])
            ->getMock();

        $filter->expects($this->once())->method('importPhotos')
            ->with([ 'xyzzy/fubar/norf.jpg' ], '2019-02-27 14:15:31');

        $filter->filter('<img src="xyzzy/fubar/norf.jpg">@@@@2019-02-27 14:15:31');
    }

    /**
     * Tests checkPhotoExists when a photo was found.
     */
    public function testCheckPhotoExistsWhenFound()
    {
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with(
                "SELECT `pk_content` FROM `contents` WHERE `content_type_name` = 'photo' AND `title` = ?",
                [ 'plugh.corge' ]
            )->willReturn([ 'pk_content' => 22062 ]);

        $method = new \ReflectionMethod($this->filter, 'checkPhotoExists');
        $method->setAccessible(true);

        $this->assertEquals(22062, $method->invokeArgs($this->filter, [ 'plugh.corge' ]));
    }

    /**
     * Tests checkPhotoExists when exception.
     */
    public function testCheckPhotoExistsWhenException()
    {
        $this->conn->expects($this->once())->method('fetchAssoc')
            ->with(
                "SELECT `pk_content` FROM `contents` WHERE `content_type_name` = 'photo' AND `title` = ?",
                [ 'plugh.corge' ]
            )->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->filter, 'checkPhotoExists');
        $method->setAccessible(true);

        $this->assertEquals(null, $method->invokeArgs($this->filter, [ 'plugh.corge' ]));
    }

    /**
     * Tests insertPhotoTranslation.
     */
    public function testInsertPhotoTranslationWhenException()
    {
        $this->conn->expects($this->once())->method('insert')
            ->with('url', [
                'content_type' => 'photo',
                'source'       => 'norf.grault',
                'target'       => 6014,
                'type'         => 1,
                'enabled'      => 1,
                'redirection'  => 1
            ]);

        $method = new \ReflectionMethod($this->filter, 'insertPhotoTranslation');
        $method->setAccessible(true);

        $method->invokeArgs($this->filter, [ 6014, 'norf.grault' ]);
    }
}
