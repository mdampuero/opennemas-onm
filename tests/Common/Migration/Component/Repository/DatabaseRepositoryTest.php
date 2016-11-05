<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Repository;

use Common\Migration\Component\Repository\DatabaseRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for DatabaseRepository class.
 */
class DatabaseRepositoryTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->config = [
            'database'   => 'foobar',
            'connection' => [],
            'mapping'    => [
                'table'  => 'frog',
                'id'     => 'id',
                'filter' => 'id > 10'
            ]
        ];

        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'selectDatabase' ])
            ->getMock();

        $this->tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\MigrationTracker')
            ->disableOriginalConstructor()
            ->setMethods([ 'getParsedSourceIds' ])
            ->getMock();

        $this->conn->expects($this->any())->method('selectDatabase')->with('foobar');

        $this->repository = new DatabaseRepository($this->config, $this->tracker);

        $property = new \ReflectionProperty($this->repository, 'conn');
        $property->setAccessible(true);
        $property->setValue($this->repository, $this->conn);
    }

    /**
     * Tests count.
     */
    public function testCount()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT COUNT(*) as total FROM frog WHERE id > 10')
            ->willReturn([ [ 'total' => 10 ] ]);

        $this->assertEquals(10, $this->repository->count());
    }

    /**
     * Tests next.
     */
    public function testNext()
    {
        $this->tracker->expects($this->once())->method('getParsedSourceIds')
            ->willReturn([ 1 ]);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT * FROM frog WHERE id NOT IN (1) AND id > 10 LIMIT 1')
            ->willReturn([ [ 'gorp' => 'grault' ] ]);

        $this->assertEquals([ 'gorp' => 'grault' ], $this->repository->next());
    }

    /**
     * Tests next when no more items to migrate left.
     */
    public function testNextWhenNoMoreResults()
    {
        $this->tracker->expects($this->once())->method('getParsedSourceIds')
            ->willReturn([ 1 ]);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT * FROM frog WHERE id NOT IN (1) AND id > 10 LIMIT 1')
            ->willReturn([]);

        $this->assertFalse($this->repository->next());
    }
}
