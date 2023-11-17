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

use Common\Model\Database\Repository\TagRepository;
use Opennemas\Orm\Core\Metadata;

/**
 * Defines test cases for TagRepository class.
 */
class TagRepositoryTest extends \PHPUnit\Framework\TestCase
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
            'name' => 'Tag',
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
                    'table' => 'contents_tags',
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
            new TagRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests countContents when a single id provided and contents found.
     */
    public function testCountContentsWhenId()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT tag_id AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents_tags '
                    . 'WHERE tag_id IN (?) '
                    . 'GROUP BY tag_id',
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
                'SELECT tag_id AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents_tags '
                    . 'WHERE tag_id IN (?) '
                    . 'GROUP BY tag_id',
                [ [ 1, 2, 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn([]);

        $this->assertEmpty($this->repository->countContents([ 1, 2, 4 ]));
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
                    . ' FROM contents_tags INNER JOIN contents'
                    . ' ON contents_tags.content_id = contents.pk_content'
                    . ' WHERE tag_id IN (?)',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn($contents);

        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'UPDATE IGNORE contents_tags SET tag_id = ?'
                    . ' WHERE tag_id IN (?)',
                [ 7, [ 4 ] ],
                [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with(
                'DELETE FROM contents_tags WHERE tag_id IN (?)',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

        $this->assertEquals($contents, $this->repository->moveContents(4, 7));
    }

    /**
     * Tests moveContents when no contents found.
     */
    public function testMoveContentsWhenNoContents()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT content_id AS "id", content_type_name AS "type"'
                    . ' FROM contents_tags INNER JOIN contents'
                    . ' ON contents_tags.content_id = contents.pk_content'
                    . ' WHERE tag_id IN (?)',
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
}
