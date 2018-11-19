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

/**
 * Defines test cases for class class.
 */
class TrackerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'insert', 'fetchAll' ])
            ->getMock();

        $this->tracker = new Tracker($this->conn, [
            'type'   => 'simple_id',
            'fields' => [ 'foo' ]
        ]);
    }

    /**
     * Tests add.
     */
    public function testAdd()
    {
        $this->conn->expects($this->once())->method('insert')
            ->with('migration_fix', [ 'foo' => 1 ]);

        $this->tracker->add(1);
    }

    /**
     * Tests count.
     */
    public function testCount()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with("SELECT COUNT(*) AS total FROM migration_fix")
            ->willReturn([ [ 'total' => 1 ] ]);

        $this->assertEquals(1, $this->tracker->count());
    }

    /**
     * Tests end.
     */
    public function testEnd()
    {
        $this->conn->expects($this->at(0))->method('executeQuery')
            ->with('DROP TABLE IF EXISTS migration_fix');

        $this->tracker->end();
    }

    /**
     * Tests start.
     */
    public function testStart()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with('CREATE TABLE IF NOT EXISTS migration_fix(foo VARCHAR(255) DEFAULT NULL)');

        $this->tracker->start();
    }
}
