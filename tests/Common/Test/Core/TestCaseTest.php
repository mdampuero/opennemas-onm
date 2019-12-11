<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Test\Core;

use Common\Test\Core\TestCase;

/**
 * Defines test cases for TestCase class.
 */
class TestCaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
            ->setMethods([
                'depth', 'files', 'getIterator', 'hasResults', 'in', 'name'
            ])->getMock();

        $this->testCase = $this->getMockBuilder('Common\Test\Core\TestCase')
            ->setMethods([ 'getFinder' ])
            ->getMock();

        $this->testCase->expects($this->any())->method('getFinder')
            ->willReturn($this->finder);
    }

    /**
     * Tests getFinder.
     */
    public function testGetFinder()
    {
        $testCase = new TestCase();

        $method = new \ReflectionMethod($testCase, 'getFinder');
        $method->setAccessible(true);

        $this->assertInstanceOf(
            'Symfony\Component\Finder\Finder',
            $method->invokeArgs($testCase, [])
        );
    }

    /**
     * Test loadFixture when fixture found.
     */
    public function testLoadFixtureWhenFixture()
    {
        $method = new \ReflectionMethod($this->testCase, 'loadFixture');
        $method->setAccessible(true);

        $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContents' ])
            ->getMock();

        $this->finder->expects($this->once())->method('in')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('name')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('files')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('depth')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('hasResults')
            ->willReturn(true);
        $this->finder->expects($this->once())->method('getIterator')
            ->willReturn(new \ArrayIterator([ $file ]));

        $file->expects($this->once())->method('getContents')
            ->willReturn('thud glork');

        $this->assertEquals(
            'thud glork',
            $method->invokeArgs($this->testCase, [ 'glorp.txt' ])
        );
    }

    /**
     * Test loadFixture when no fixture found.
     */
    public function testLoadFixtureWhenNoFixture()
    {
        $method = new \ReflectionMethod($this->testCase, 'loadFixture');
        $method->setAccessible(true);

        $this->finder->expects($this->once())->method('in')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('name')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('files')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('depth')
            ->willReturn($this->finder);
        $this->finder->expects($this->once())->method('hasResults')
            ->willReturn(false);

        $this->assertEmpty($method->invokeArgs($this->testCase, [ 'glorp.txt' ]));
    }
}
