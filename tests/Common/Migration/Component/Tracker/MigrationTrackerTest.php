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

use Common\Migration\Component\Tracker\MigrationTracker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for class class.
 */
class MigrationTrackerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll' ])
            ->getMock();

        $this->tracker = new MigrationTracker($this->conn);
    }

    /**
     * Tests load.
     */
    public function testLoad()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with("SELECT * FROM translation_ids WHERE type = 'garply'")
            ->willReturn([
                [
                    'pk_content_old' => 'frog',
                    'pk_content'     => 'fubar',
                    'type'           => 'wibble',
                    'slug'           => 'glorp'
                ]
            ]);

        $this->tracker->load('garply');

        $this->assertTrue($this->tracker->isParsed('frog', 'wibble'));
    }

    /**
     * Tests persist.
     */
    public function testPersist()
    {
        $property = new \ReflectionProperty($this->tracker, 'parsed');
        $property->setAccessible(true);

        $property->setValue($this->tracker, [
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'quux', 'target_id' => 'corge' ],
            [ 'source_id' => 'xyzzy', 'type' => 'plugh', 'slug' => 'fubar', 'target_id' => 'thud' ]
        ]);

        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'REPLACE INTO translation_ids VALUES (?,?,?,?),(?,?,?,?)',
                [ 'xyzzy', 'corge', 'plugh', 'quux', 'xyzzy', 'thud', 'plugh', 'fubar'  ]
            );

        $this->tracker->persist();
    }
}
