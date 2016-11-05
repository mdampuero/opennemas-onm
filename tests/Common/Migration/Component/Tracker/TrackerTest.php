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

        $property = new \ReflectionProperty($this->tracker, 'type');
        $property->setAccessible(true);
        $property->setValue($this->tracker, 'plugh');
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
            [ 'source_id' => 'waldo', 'type' => 'plugh', 'slug' => null, 'target_id' => 'fred' ],
        ]);

        $this->assertFalse($this->tracker->isParsed('corge'));
        $this->assertFalse($this->tracker->isParsed('waldo', 'gorp'));
        $this->assertTrue($this->tracker->isParsed('waldo'));
        $this->assertTrue($this->tracker->isParsed('waldo', null));
    }

    /**
     * Tests getParsed.
     */
    public function testGetParsed()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'waldo', 'type' => 'plugh', 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ]);

        $this->assertEquals([
            [ 'source_id' => 'waldo', 'type' => 'plugh', 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ], $this->tracker->getParsed());
    }

    /**
     * Tests getParsedSourceIds.
     */
    public function testGetParsedSourceIds()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'waldo', 'type' => 'plugh', 'slug' => null, 'target_id' => 'fred' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ]
        ]);

        $this->assertEquals([ 'waldo', 'xyzzy' ], $this->tracker->getParsedSourceIds());
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
        $this->assertEquals('xyzzy', $this->tracker->getSourceId('corge', 'quux'));
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
        $this->assertEquals('thud', $this->tracker->getTargetId('xyzzy', 'fubar'));
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
