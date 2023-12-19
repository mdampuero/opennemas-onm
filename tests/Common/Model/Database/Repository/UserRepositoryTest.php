<?php

namespace Tests\Common\Model\Database\Repository;

use Common\Model\Database\Repository\UserRepository;
use Opennemas\Orm\Core\Metadata;

class UserRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'fetchArray' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name'  => 'User',
            'class' => 'Common\Model\Entity\User',
            'properties' => [
                'id'   => 'integer',
                'name' => 'string',
            ],
            'converters' => [
                'default' => [
                    'class' => 'Opennemas\Orm\Database\Data\Converter\BaseConverter'
                ]
            ],
            'mapping' => [
                'database' => [
                    'table' => 'user',
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
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository =
            new UserRepository('foo', $this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests findSubscribers.
     */
    public function testfindSubscribers()
    {
        $user1 = [
            'id' => 1,
            'email' => 'gorp@flob.org',
            'name' => 'gorp',
            'activated' => 1,
            'user_groups' => '7, 10',
        ];

        $user2 = [
            'id' => 2,
            'email' => 'grault@flob.org',
            'name' => 'grault',
            'activated' => 1,
            'user_groups'  => null,
        ];

        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT id, email, name, activated,'
                . ' GROUP_CONCAT(DISTINCT user_group_id) as user_groups FROM users '
                . ' LEFT JOIN user_user_group ON user_user_group.user_id = id'
                . ' WHERE type != 0'
                . ' GROUP BY id'
            )->willReturn([ $user1, $user2 ]);

        $this->conn->expects($this->at(1))->method('fetchAll')
            ->with('SELECT * FROM usermeta')
            ->willReturn([
                [ 'user_id' => 1, 'meta_key' => 'register_date', 'meta_value' => '2020-02-13 13:30:00' ],
                [ 'user_id' => 1, 'meta_key' => 'foobar', 'meta_value' => 'baz' ],
                [ 'user_id' => 9, 'meta_key' => 'foobar', 'meta_value' => 'baz' ],
             ]);

        $user1['user_groups'] = [7, 10];
        $user2['user_groups'] = [];

        $user1['register_date'] = '2020-02-13 13:30:00';
        $user1['foobar']        = 'baz';

        $this->assertEquals(
            [ $user1['id'] => $user1, $user2['id'] => $user2 ],
            $this->repository->findSubscribers()
        );
    }

    /**
     * Tests findSubscribers.
     */
    public function testfindAuthors()
    {
        $user1 = [
            'id' => 1,
            "username" => "Editorial",
            "password" => null,
            "url" => null,
            "bio" => "",
            "avatar_img_id" => null,
            'email' => 'gorp@flob.org',
            'name' => 'gorp',
            "slug" => "Editorial",
            "type" => 0,
            "token" => null,
            'activated' => 1,
            'user_groups' => '3, 7',
            'is_blog' => null
        ];

        $user2 = [
            'id' => 1,
            "username" => "Editorial",
            "password" => null,
            "url" => null,
            "bio" => "",
            "avatar_img_id" => null,
            'email' => 'gorp@flob.org',
            'name' => 'gorp',
            "slug" => "Editorial",
            "type" => 0,
            "token" => null,
            'activated' => 1,
            'user_groups' => '3, 7',
            'is_blog' => null
        ];

        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                "SELECT users.*,
                    GROUP_CONCAT(user_user_group.user_group_id) AS user_groups,
                    usermeta.meta_value AS is_blog
                FROM users
                LEFT JOIN user_user_group ON users.id = user_user_group.user_id
                LEFT JOIN usermeta ON users.id = usermeta.user_id AND usermeta.meta_key = 'is_blog'
                WHERE user_user_group.user_group_id = 3
                GROUP BY users.id ORDER BY users.name ASC;
                "
            )->willReturn([ $user1, $user2 ]);

        $this->assertEquals(
            [ $user1, $user2 ],
            $this->repository->findAuthors()
        );
    }

     /**
     * Tests countContents when a single id provided and contents found.
     */
    public function testCountContentsWhenId()
    {
        $this->conn->expects($this->once())->method('fetchAll')
            ->with(
                'SELECT fk_author AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents '
                    . 'WHERE fk_author IN (?) '
                    . 'GROUP BY fk_author',
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
                'SELECT fk_author AS "id", COUNT(1) AS "contents" '
                    . 'FROM contents '
                    . 'WHERE fk_author IN (?) '
                    . 'GROUP BY fk_author',
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
                'SELECT pk_content AS "id", content_type_name AS "type"'
                    . ' FROM contents'
                    . ' WHERE fk_author IN (?)',
                [ [ 4 ] ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            )->willReturn($contents);
        $this->conn->expects($this->at(1))->method('executeQuery')
            ->with(
                'UPDATE IGNORE contents SET fk_author = ?'
                    . ' WHERE fk_author IN (?)',
                [ 7, [ 4 ] ],
                [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );
        $this->conn->expects($this->at(2))->method('executeQuery')
            ->with(
                'DELETE FROM contents WHERE fk_author IN (?)',
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
                'SELECT pk_content AS "id", content_type_name AS "type"'
                    . ' FROM contents'
                    . ' WHERE fk_author IN (?)',
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
