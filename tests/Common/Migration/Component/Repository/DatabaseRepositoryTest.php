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

/**
 * Defines test cases for DatabaseRepository class.
 */
class DatabaseRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->config = [
            'connection' => [],
            'source'     => [
                'database' => 'foobar',
                'table'    => 'frog',
                'id'       => ['id'],
                'filter'   => 'title LIKE "%foo%"'
            ]
        ];

        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll', 'selectDatabase' ])
            ->getMock();

        $this->tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\Tracker')
            ->disableOriginalConstructor()
            ->setMethods([ 'add', 'count' ])
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
            ->with('SELECT COUNT(*) as total FROM migration_fix_items')
            ->willReturn([ [ 'total' => 8 ] ]);

        $this->assertEquals(8, $this->repository->count());
    }

    /**
     * Tests countAll.
     */
    public function testCountAll()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT COUNT(*) as total FROM frog WHERE title LIKE "%foo%"')
            ->willReturn([ [ 'total' => 10 ] ]);

        $this->assertEquals(10, $this->repository->countAll());
    }

    /**
     * Tests countFixed.
     */
    public function testCountFixed()
    {
        $this->tracker->expects($this->once())->method('count')->willReturn(10);

        $this->assertEquals(10, $this->repository->countFixed());
    }

    /**
     * Tests end.
     */
    public function testEnd()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with('DROP TABLE IF EXISTS migration_fix_items');

        $this->repository->end();
    }

    /**
     * Tests next.
     */
    public function testNext()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT frog.* FROM frog'
                . ' JOIN (SELECT * FROM migration_fix_items LIMIT 1)'
                . ' fixing ON fixing.id = frog.id'
            )->willReturn([ [ 'id' => 'grault' ] ]);

        $this->conn->expects($this->once())->method('executeQuery')
            ->with('DELETE FROM migration_fix_items WHERE id = "grault"');

        $this->assertEquals([ 'id' => 'grault' ], $this->repository->next());
    }

    /**
     * Tests next when no more items to migrate left.
     */
    public function testNextWhenNoMoreResults()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT frog.* FROM frog JOIN (SELECT * FROM migration_fix_items LIMIT 1)' .
                ' fixing ON fixing.id = frog.id')
            ->willReturn([]);

        $this->assertFalse($this->repository->next());
    }

    /**
     * Tests prepare.
     */
    public function testPrepare()
    {
        $sqls = [
            'CREATE VIEW `mumble` SELECT * FROM `gorp`',
            'CREATE VIEW `baz` SELECT * FROM `xyzzy`',
        ];

        $this->conn->expects($this->at(0))->method('executeQuery')
            ->with('CREATE VIEW `mumble` SELECT * FROM `gorp`');
        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with('CREATE VIEW `baz` SELECT * FROM `xyzzy`');

        $this->repository->prepare($sqls);
    }

    /**
     * Tests start.
     */
    public function testStart()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'CREATE TABLE IF NOT EXISTS migration_fix_items (PRIMARY KEY (id))'
                . ' AS SELECT DISTINCT id FROM frog WHERE title LIKE "%foo%"'
            );


        $this->repository->start();
    }
}
