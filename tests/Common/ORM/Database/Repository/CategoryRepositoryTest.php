<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Database\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Database\Repository\CategoryRepository;

/**
 * Defines test cases for CategoryRepository class.
 */
class CategoryRepositoryTest extends \PHPUnit\Framework\TestCase
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

        $this->metadata = new Metadata([
            'name' => 'Category',
            'properties' => [
                'pk_content_category' => 'integer',
                'name'                => 'string',
            ],
            'mapping' => [
                'database' => [
                    'table' => 'contents_categories',
                    'columns' => [
                        'pk_content_category' => [
                            'type'    => 'integer',
                            'options' => [ 'default' => null ]
                        ],
                        'name' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ]
                    ],
                    'index' => [
                        [
                            'primary' => true,
                            'columns' => [ 'pk_content_category' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository =
            new CategoryRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests countContents when a single id provided and contents found.
     */
    public function testCountContentsWhenId()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_fk_content_category AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents_categories '
                    . 'WHERE pk_fk_content_category IN (?) '
                    . 'GROUP BY pk_fk_content_category',
                [ [ 1 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([
                [ 'id' => 1, 'contents' => 10 ],
            ]);

        $this->assertEquals(
            [ 1 => 10 ],
            $this->repository->countContents(1)
        );
    }

    /**
     * Tests countContents when a list of ids provided and contents found.
     */
    public function testCountContentsWhenList()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_fk_content_category AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents_categories '
                    . 'WHERE pk_fk_content_category IN (?) '
                    . 'GROUP BY pk_fk_content_category',
                [ [ 1, 2, 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([
                [ 'id' => 1, 'contents' => 10 ],
                [ 'id' => 4, 'contents' => 15 ]
            ]);

        $this->assertEquals(
            [ 1 => 10, 4 => 15 ],
            $this->repository->countContents([ 1, 2, 4 ])
        );
    }

    /**
     * Tests countContents when no ids provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCountContentsWhenNoIdsProvided()
    {
        $this->repository->countContents(null);
    }

    /**
     * Tests countContents when a list of ids provided but no contents found.
     */
    public function testCountContentsWhenNoContentsFound()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_fk_content_category AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents_categories '
                    . 'WHERE pk_fk_content_category IN (?) '
                    . 'GROUP BY pk_fk_content_category',
                [ [ 1, 2, 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([]);

        $this->assertEmpty($this->repository->countContents([ 1, 2, 4 ]));
    }

    /**
     * Tests countContents when a single id provided and contents found.
     */
    public function testFindContentsWhenId()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_content AS "id", content_type_name AS "type" '
                    . 'FROM contents '
                    . 'LEFT JOIN contents_categories '
                    . 'ON pk_content = pk_fk_content '
                    . 'WHERE pk_fk_content_category IN (?)',
                [ [ 1 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([ [ 'id' => 1, 'type' => 'flob' ] ]);

        $this->assertEquals(
            [ [ 'id' => 1, 'type' => 'flob' ] ],
            $this->repository->findContents(1)
        );
    }

    /**
     * Tests findContents when a list of ids provided and contents found.
     */
    public function testFindContentsWhenList()
    {
        $contents = [
            [ 'id' => 1, 'type' => 'grault' ],
            [ 'id' => 4, 'type' => 'thud' ]
        ];

        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_content AS "id", content_type_name AS "type" '
                    . 'FROM contents '
                    . 'LEFT JOIN contents_categories '
                    . 'ON pk_content = pk_fk_content '
                    . 'WHERE pk_fk_content_category IN (?)',
                [ [ 1, 2, 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn($contents);

        $this->assertEquals($contents, $this->repository->findContents([ 1, 2, 4 ]));
    }

    /**
     * Tests findContents when no ids provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFindContentsWhenNoIdsProvided()
    {
        $this->repository->findContents(null);
    }

    /**
     * Tests findContents when a list of ids provided but no contents found.
     */
    public function testFindContentsWhenNoContentsFound()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT pk_content AS "id", content_type_name AS "type" '
                    . 'FROM contents '
                    . 'LEFT JOIN contents_categories '
                    . 'ON pk_content = pk_fk_content '
                    . 'WHERE pk_fk_content_category IN (?)',
                [ [ 1, 2, 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([]);

        $this->assertEmpty($this->repository->findContents([ 1, 2, 4 ]));
    }

    /**
     * Tests countContents when a single id provided and contents found.
     */
    public function testMoveContentsWhenId()
    {
        $repository = $this->getMockBuilder('Common\ORM\Database\Repository\CategoryRepository')
            ->setMethods([ 'findContents' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();

        $repository->expects($this->once())->method('findContents')
            ->with([ 4 ])->willReturn([ [ 'id' => 52, 'type' => 'wobble' ] ]);

        $this->conn->expects($this->at(0))->method('executeQuery')
            ->with(
                'REPLACE INTO contents_categories (pk_fk_content_category, pk_fk_content) '
                    . 'VALUES (?,?)',
                [ 7, 52 ],
                [ \PDO::PARAM_INT, \PDO::PARAM_INT ]
            )->willReturn([ [ 'id' => 1, 'type' => 'flob' ] ]);

        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'DELETE FROM contents_categories WHERE pk_fk_content_category IN (?)',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([ [ 'id' => 1, 'type' => 'flob' ] ]);


        $this->assertEquals(
            [ [ 'id' => 52, 'type' => 'wobble' ] ],
            $repository->moveContents(4, 7)
        );
    }

    /**
     * Tests moveContents when a list of ids provided and contents found.
     */
    public function testMoveContentsWhenList()
    {
        $repository = $this->getMockBuilder('Common\ORM\Database\Repository\CategoryRepository')
            ->setMethods([ 'findContents' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();

        $contents = [
            [ 'id' => 94, 'type' => 'grault' ],
            [ 'id' => 105, 'type' => 'thud' ]
        ];

        $repository->expects($this->once())->method('findContents')
            ->with([ 4, 5 ])->willReturn($contents);

        $this->conn->expects($this->at(0))->method('executeQuery')
            ->with(
                'REPLACE INTO contents_categories (pk_fk_content_category, pk_fk_content) '
                    . 'VALUES (?,?),(?,?)',
                [ 7, 94, 7, 105 ],
                [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
            )->willReturn([ [ 'id' => 1, 'type' => 'flob' ] ]);

        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'DELETE FROM contents_categories WHERE pk_fk_content_category IN (?)',
                [ [ 4, 5 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([ [ 'id' => 1, 'type' => 'flob' ] ]);


        $this->assertEquals($contents, $repository->moveContents([ 4, 5 ], 7));
    }

    /**
     * Tests moveContents when no ids provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMoveContentsWhenNoIdsProvided()
    {
        $this->repository->moveContents(null, null);
    }

    /**
     * Tests moveContents when a list of ids provided but no contents found.
     */
    public function testMoveContentsWhenNoContentsFound()
    {
        $repository = $this->getMockBuilder('Common\ORM\Database\Repository\CategoryRepository')
            ->setMethods([ 'findContents' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();

        $repository->expects($this->once())->method('findContents')
            ->with([ 4 ])->willReturn([]);

        $this->assertEmpty($repository->moveContents(4, 7));
    }

    /**
     * Tests countContents when a single id provided and contents found.
     */
    public function testRemoveContentsWhenId()
    {
        $repository = $this->getMockBuilder('Common\ORM\Database\Repository\CategoryRepository')
            ->setMethods([ 'findContents' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();

        $repository->expects($this->once())->method('findContents')
            ->with([ 4 ])->willReturn([ [ 'id' => 52, 'type' => 'wobble' ] ]);

        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'DELETE FROM contents WHERE pk_content IN (?)',
                [ [ 52 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->assertEquals(
            [ [ 'id' => 52, 'type' => 'wobble' ] ],
            $repository->removeContents(4)
        );
    }

    /**
     * Tests moveContents when a list of ids provided and contents found.
     */
    public function testRemoveContentsWhenList()
    {
        $repository = $this->getMockBuilder('Common\ORM\Database\Repository\CategoryRepository')
            ->setMethods([ 'findContents' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();

        $contents = [
            [ 'id' => 94, 'type' => 'grault' ],
            [ 'id' => 105, 'type' => 'thud' ]
        ];

        $repository->expects($this->once())->method('findContents')
            ->with([ 4, 5 ])->willReturn($contents);

        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'DELETE FROM contents WHERE pk_content IN (?)',
                [ [ 94, 105 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([ [ 'id' => 1, 'type' => 'flob' ] ]);


        $this->assertEquals($contents, $repository->removeContents([ 4, 5 ]));
    }

    /**
     * Tests moveContents when no ids provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveContentsWhenNoIdsProvided()
    {
        $this->repository->removeContents(null, null);
    }

    /**
     * Tests moveContents when a list of ids provided but no contents found.
     */
    public function testRemoveContentsWhenNoContentsFound()
    {
        $repository = $this->getMockBuilder('Common\ORM\Database\Repository\CategoryRepository')
            ->setMethods([ 'findContents' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();

        $repository->expects($this->once())->method('findContents')
            ->with([ 4, 7 ])->willReturn([]);

        $this->assertEmpty($repository->removeContents([ 4, 7 ]));
    }
}
