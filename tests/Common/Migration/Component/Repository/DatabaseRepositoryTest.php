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
                'filter' => 'title LIKE "%foo%"'
            ]
        ];

        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll', 'selectDatabase' ])
            ->getMock();

        $this->tracker = $this->getMockBuilder('Common\Migration\Component\Tracker\MigrationTracker')
            ->disableOriginalConstructor()
            ->setMethods([ 'getParsed' ])
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
        $this->tracker->expects($this->once())->method('getParsed')
            ->willReturn([ 1, 2 ]);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT COUNT(*) as total FROM frog WHERE id NOT IN ("1", "2") AND title LIKE "%foo%"')
            ->willReturn([ [ 'total' => 10 ] ]);

        $this->assertEquals(10, $this->repository->count());
    }

    /**
     * Tests countAll.
     */
    public function testCountAll()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT COUNT(*) as total FROM frog')
            ->willReturn([ [ 'total' => 10 ] ]);

        $this->assertEquals(10, $this->repository->countAll());
    }

    /**
     * Tests countMigrated.
     */
    public function testCountMigrated()
    {
        $this->tracker->expects($this->once())->method('getParsed')
            ->willReturn([ 1, 2 ]);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT COUNT(*) as total FROM frog WHERE id IN ("1", "2") AND title LIKE "%foo%" ORDER BY id ASC')
            ->willReturn([ [ 'total' => 10 ] ]);

        $this->assertEquals(10, $this->repository->countMigrated());
    }

    /**
     * Tests next.
     */
    public function testNext()
    {
        $this->tracker->expects($this->once())->method('getParsed')
            ->willReturn([ 1, 2 ]);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT * FROM frog WHERE id NOT IN ("1", "2") AND title LIKE "%foo%" ORDER BY id ASC LIMIT 1')
            ->willReturn([ [ 'gorp' => 'grault' ] ]);

        $this->assertEquals([ 'gorp' => 'grault' ], $this->repository->next());
    }

    /**
     * Tests next when no more items to migrate left.
     */
    public function testNextWhenNoMoreResults()
    {
        $this->tracker->expects($this->once())->method('getParsed')
            ->willReturn(1756);

        $this->conn->expects($this->once())->method('fetchAll')
            ->with('SELECT * FROM frog WHERE id > "1756" AND title LIKE "%foo%" ORDER BY id ASC LIMIT 1')
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
}
