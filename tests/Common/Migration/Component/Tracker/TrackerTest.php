<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Tracker;

use Common\Migration\Component\Tracker\Tracker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Tracker class.
 */
class TrackerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\Tracker')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    public function testsAdd()
    {
        $this->tracker->add('foobar', 'thud');
        $this->assertTrue($this->tracker->isParsed('foobar'));
        $this->assertFalse($this->tracker->isParsed('foobar', 'frog'));

        $this->tracker->add('foobar', 'thud', 'frog');
        $this->assertTrue($this->tracker->isParsed('foobar', 'frog'));
    }

    /**
     * Tests isParsed.
     */
    public function testIsParsed()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'waldo', 'type' => null, 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ]);

        $this->assertFalse($this->tracker->isParsed('corge'));
        $this->assertFalse($this->tracker->isParsed('waldo', null, 'gorp'));
        $this->assertTrue($this->tracker->isParsed('waldo'));
        $this->assertTrue($this->tracker->isParsed('waldo', null, null));
        $this->assertFalse($this->tracker->isParsed('xyzzy', 'foobar'));
        $this->assertFalse($this->tracker->isParsed('xyzzy', 'foobar', 'quux'));
        $this->assertFalse($this->tracker->isParsed('xyzzy', 'plugh', 'gorp'));
        $this->assertTrue($this->tracker->isParsed('xyzzy'));
        $this->assertTrue($this->tracker->isParsed('xyzzy', null, 'quux'));
        $this->assertTrue($this->tracker->isParsed('xyzzy', 'plugh'));
    }

    /**
     * Tests getParsed with and without arguments.
     */
    public function testGetParsed()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'waldo', 'type' => null, 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ]);

        $this->assertEquals([
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ], $this->tracker->getParsed('plugh'));

        $this->assertEquals([
            [ 'source_id' => 'waldo', 'type' => null, 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ], $this->tracker->getParsed());
    }

    /**
     * Tests getSourceId when the content was translated previously.
     */
    public function testGetSourceId()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'waldo', 'type' => null, 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'fubar', 'target_id' => 'thud' ]
        ]);

        $this->assertEquals('waldo', $this->tracker->getSourceId('fred'));
        $this->assertEquals('xyzzy', $this->tracker->getSourceId('corge', null, 'quux'));
        $this->assertEquals('xyzzy', $this->tracker->getSourceId('corge', 'plugh', 'quux'));
        $this->assertEquals('xyzzy', $this->tracker->getSourceId('thud'));
    }

    /**
     * Tests getSourceId when the list of translated contents of the type is
     * empty.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotParsedException
     */
    public function testGetSourceIdWhenNoOtherContentOfTypeParsed()
    {
        $this->tracker->getSourceId('fred');
    }

    /**
     * Tests getSourceId when the content was not translated previously.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotParsedException
     */
    public function testGetSourceIdWhenNoContentParsed()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
        ]);

        $this->tracker->getSourceId('fred');
    }

    /**
     * Tests getTargetId when the content was translated previously.
     */
    public function testGetTargetId()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'fubar', 'target_id' => 'thud' ]
        ]);

        $this->assertEquals('corge', $this->tracker->getTargetId('xyzzy'));
        $this->assertEquals('corge', $this->tracker->getTargetId('xyzzy', 'plugh'));
        $this->assertEquals('thud', $this->tracker->getTargetId('xyzzy', 'plugh', 'fubar'));
    }

    /**
     * Tests getTargetId when the list of translated contents of the type is
     * empty.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotParsedException
     */
    public function testGetTargetIdWhenNoOtherContentOfTypeParsed()
    {
        $this->tracker->getTargetId('fred');
    }

    /**
     * Tests getTargetId when the content was not translated previously.
     *
     * @expectedException \Common\Migration\Component\Exception\EntityNotParsedException
     */
    public function testGetTargetIdWhenNoContentParsed()
    {
        $this->tracker->getTargetId('fred');
    }
}
