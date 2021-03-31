<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Model\Database\Repository;

use Common\Model\Database\Repository\CategoryRepository;
use Opennemas\Orm\Core\Metadata;

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
        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'executeQuery', 'fetchAll' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'Category',
            'properties' => [
                'id' => 'integer',
                'name'                => 'string',
            ],
            'converters' => [
                'default' => [
                    'class' => 'Opennemas\Orm\Database\Data\Converter\BaseConverter'
                ]
            ],
            'mapping' => [
                'database' => [
                    'table' => 'content_category',
                    'columns' => [
                        'id' => [
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
                            'columns' => [ 'id' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
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
                'SELECT category_id AS "id", COUNT(1) AS "contents" '
                    . 'FROM content_category '
                    . 'WHERE category_id IN (?) '
                    . 'GROUP BY category_id',
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
                'SELECT category_id AS "id", COUNT(1) AS "contents" '
                    . 'FROM content_category '
                    . 'WHERE category_id IN (?) '
                    . 'GROUP BY category_id',
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
                'SELECT category_id AS "id", COUNT(1) AS "contents" '
                    . 'FROM content_category '
                    . 'WHERE category_id IN (?) '
                    . 'GROUP BY category_id',
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
                    . 'LEFT JOIN content_category '
                    . 'ON pk_content = content_id '
                    . 'WHERE category_id IN (?)',
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
                    . 'LEFT JOIN content_category '
                    . 'ON pk_content = content_id '
                    . 'WHERE category_id IN (?)',
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
                    . 'LEFT JOIN content_category '
                    . 'ON pk_content = content_id '
                    . 'WHERE category_id IN (?)',
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
        $contents = [ [ 'id' => 8326, 'type' => 'baz' ] ];

        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT content_id AS "id", content_type_name AS "type"'
                    . ' FROM content_category INNER JOIN contents'
                    . ' ON content_category.content_id = contents.pk_content'
                    . ' WHERE category_id IN (?)',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn($contents);

        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'UPDATE IGNORE content_category SET category_id = ?'
                    . ' WHERE category_id IN (?)',
                [ 7, [ 4 ] ],
                [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with(
                'DELETE FROM content_category WHERE category_id IN (?)',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->assertEquals($contents, $this->repository->moveContents(4, 7));
    }

    /**
     * Tests moveContents when a list of ids provided and contents found.
     */
    public function testMoveContentsWhenList()
    {
        $contents = [
            [ 'id' => 8326, 'type' => 'baz' ],
            [ 'id' => 13481, 'type' => 'glork' ]
        ];

        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT content_id AS "id", content_type_name AS "type"'
                    . ' FROM content_category INNER JOIN contents'
                    . ' ON content_category.content_id = contents.pk_content'
                    . ' WHERE category_id IN (?)',
                [ [ 4, 5 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn($contents);


        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'UPDATE IGNORE content_category SET category_id = ?'
                    . ' WHERE category_id IN (?)',
                [ 7, [ 4, 5 ] ],
                [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with(
                'DELETE FROM content_category WHERE category_id IN (?)',
                [ [ 4, 5 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );


        $this->assertEquals($contents, $this->repository->moveContents([ 4, 5 ], 7));
    }

    /**
     * Tests moveContents when no contents found.
     */
    public function testMoveContentsWhenNoContents()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT content_id AS "id", content_type_name AS "type"'
                    . ' FROM content_category INNER JOIN contents'
                    . ' ON content_category.content_id = contents.pk_content'
                    . ' WHERE category_id IN (?)',
                [ [ 4, 5 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([]);

        $this->assertEquals([], $this->repository->moveContents([ 4, 5 ], 7));
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
     * Tests countContents when a single id provided and contents found.
     */
    public function testRemoveContentsWhenId()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'DELETE FROM contents WHERE pk_content IN (SELECT content_id'
                . ' FROM content_category WHERE category_id IN (?))',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->repository->removeContents(4);
    }

    /**
     * Tests moveContents when a list of ids provided and contents found.
     */
    public function testRemoveContentsWhenList()
    {
        $this->conn->expects($this->once())->method('executeQuery')
            ->with(
                'DELETE FROM contents WHERE pk_content IN (SELECT content_id'
                . ' FROM content_category WHERE category_id IN (?))',
                [ [ 4, 5 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->repository->removeContents([ 4, 5 ]);
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
}
